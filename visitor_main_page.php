<html>
<body>
<?php
session_start();
require 'dbinfo.php';
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);

$query = "SELECT * FROM Property WHERE ApprovedBy IS NOT NULL AND IsPublic = 1";

$username = $_GET['name'];
echo "<h1>Welcome $username</h1>";
echo "<h3>All Public, Confirmed Properties</h3>";
echo "<table>";
echo "<tr>";
echo "<th>Name<a href='visitor_main_page.php?name=$username&sort_name=0'>↑</a> <a href='visitor_main_page.php?name=$username&sort_name=1'>↓</a></th>";
echo "<th>Street</th>";
echo "<th>City<a href='visitor_main_page.php?name=$username&sort_city=0'>↑</a> <a href='visitor_main_page.php?name=$username&sort_city=1'>↓</a></th>";
echo "<th>Zip</th>";
echo "<th>Size</th>";
echo "<th>PropertyType<a href='visitor_main_page.php?name=$username&sort_propertyType=0'>↑</a> <a href='visitor_main_page.php?name=$username&sort_propertyType=1'>↓</a></th>";
echo "<th>Public</th>";
echo "<th>Commercial</th>";
echo "<th>ID</th>";
echo "<th>Visits<a href='visitor_main_page.php?name=$username&sort_visits=0'>↑</a> <a href='visitor_main_page.php?name=$username&sort_visits=1'>↓</a></th>";
echo "<th>Avg. Rating<a href='visitor_main_page.php?name=$username&sort_avgRating=0'>↑</a> <a href='visitor_main_page.php?name=$username&sort_avgRating=1'>↓</a></th>";
echo "</tr>";
echo "<tr>";

if(isset($_GET['sort_name'])) {
    $temp_order = $_GET['sort_name'];
    if($temp_order==0) {
        $query .= " order by Name asc";
    } else {
        $query .= " order by Name desc";
    }
}

if(isset($_GET['sort_city'])) {
    $temp_order = $_GET['sort_city'];
    if($temp_order==0) {
        $query .= " order by City asc";
    } else {
        $query .= " order by City desc";
    }
}

if(isset($_GET['sort_propertyType'])) {
    $temp_order = $_GET['sort_propertyType'];
    if($temp_order==0) {
        $query .= " order by PropertyType asc";
    } else {
        $query .= " order by PropertyType desc";
    }
}

if(isset($_GET['sort_visits'])) {
    $temp_order = $_GET['sort_visits'];
    if($temp_order==0) {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY count(v.PropertyID) ASC";
    } else {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY count(v.PropertyID) DESC";
    }
}

if(isset($_GET['sort_avgRating'])) {
    $temp_order = $_GET['sort_avgRating'];
    if($temp_order==0) {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY AVG(Rating) ASC";
    } else {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY AVG(Rating) DESC";
    }
}

$result = mysqli_query($connection, $query);

if(isset($_POST['SearchBy'])) {
    $attr = $_POST['SearchBy'];
    if(isset($_POST['SearchTerm'])){
        $SearchTerm = $_POST['SearchTerm'];
        $query .= " AND ($attr LIKE '%$SearchTerm%')";
        $result = mysqli_query($connection, $query);
    } elseif(isset($_POST['From']) && isset($_POST['To'])) {
        $From = $_POST['From'];
        $To = $_POST['To'];
        if(is_numeric($From) && is_numeric($To)) {
            if($attr=='Visits'){
                $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                               LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                                               GROUP BY p.ID 
                                               HAVING COUNT(v.PropertyID) >= $From AND COUNT(v.PropertyID) <= $To))";
            } elseif($attr == 'Avg. Rating'){
                $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                               LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                                               GROUP BY p.ID
                                               HAVING AVG(v.Rating) >= $From AND AVG(v.Rating) <= $To))";
            }
            $result = mysqli_query($connection, $query);
        }
    }
}

if($result != False && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result))
    {
        echo "<td>" .$row['Name']. "</td>";
        echo "<td>" .$row['Street']. "</td>";
        echo "<td>" .$row['City']. "</td>";
        echo "<td>" .$row['Zip']. "</td>";
        echo "<td>" .$row['Size']. "</td>";
        echo "<td>" .$row['PropertyType']. "</td>";
        echo "<td>" .$row['IsPublic']. "</td>";
        echo "<td>" .$row['IsCommercial']. "</td>";
        echo "<td>" .str_pad($row['ID'], 5, '0', STR_PAD_LEFT). "</td>";
        echo "<td>";
        $tmp_ID = $row['ID'];
        $num_visits_query = "SELECT COUNT(*) as num_visits FROM Visit WHERE PropertyID = '$tmp_ID'";
        $num_visits_result = mysqli_query($connection, $num_visits_query);
        $tmp_row = mysqli_fetch_assoc($num_visits_result);
        echo $tmp_row['num_visits'];
        echo "</td>";
        echo "<td>";
        $avgRating_query = "SELECT AVG(Rating) as avgRating FROM Visit WHERE PropertyID = '$tmp_ID'";
        $avgRating_result = mysqli_query($connection, $avgRating_query);
        $tmp_row = mysqli_fetch_assoc($avgRating_result);
        if(is_numeric($tmp_row['avgRating'])){
            echo number_format($tmp_row['avgRating'], 2);
        }
        echo "</td>";
        echo "<td>";
        $check_log_query = "SELECT * FROM Visit WHERE Username='$username' AND PropertyID='$tmp_ID'";
        $check_log_result = mysqli_query($connection, $check_log_query);
        if(mysqli_num_rows($check_log_result) == 0){
            echo "<a href='visitor_property_page.php?username=$username&ID=".$row['ID']."'>View Property</a>";
        } else {
            echo "<a href='visitor_property_logged.php?username=$username&ID=".$row['ID']."'>View Property</a>";
        }
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No Public and Comfirmed Property";
}
echo "</table>";

// Search by
echo "<form action='visitor_main_page.php?name=$username' method='post'>
          <select id='visitorSearch' onchange='searchRange()' name='SearchBy'>
              <option value='' disabled selected>Search by</option>
              <option value='Name'>Name</option>
              <option value='Street'>Street</option>
              <option value='City'>City</option>
              <option value='Zip'>Zip</option>
              <option value='Size'>Size</option>
              <option value='PropertyType'>PropertyType</option>
              <option value='Commercial'>Commercial</option>
              <option value='ID'>ID</option>
              <option value='Visits'>Visits</option>
              <option value='Avg. Rating'>Avg. Rating</option>
          </select><br>
          <span id='term'><input type='text' placeholder='Search Term' name='SearchTerm'><br></span>
          <input value='Search Properties' type='submit'>
    </form>";

echo "<a href='visitor_history.php?name=$username'>View Visit History</a><br/>";
echo "<a href='login.php'>Log Out</a><br/>";

mysqli_close($connection);
?>

<script>
function searchRange() {
    var x = document.getElementById('visitorSearch').value;
    if(x=='Visits' || x=='Avg. Rating'){
        document.getElementById('term').innerHTML = "<input type='text' placeholder='From' name='From'> - <input type='text' placeholder='To' name='To'><br>";
    } else {
        document.getElementById('term').innerHTML = "<input type='text' placeholder='Search Term' name='SearchTerm'><br>";
    }
}
</script>

</body>
</html>
