<html>
<body>
<?php
session_start();
require 'dbinfo.php';
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);

$query = "SELECT * FROM Property WHERE ApprovedBy IS NOT NULL";

echo "<h2>Confirmed Properties</h2>";
echo "<table>";
echo "<tr>";
echo "<th>Name<a href='confirmed_properties.php?sort_name=0'>↑</a> <a href='confirmed_properties.php?sort_name=1'>↓</a></th>";
echo "<th>Street</th>";
echo "<th>City</th>";
echo "<th>Zip</th>";
echo "<th>Size<a href='confirmed_properties.php?sort_size=0'>↑</a> <a href='confirmed_properties.php?sort_size=1'>↓</a></th>";
echo "<th>PropertyType<a href='confirmed_properties.php?sort_propertyType=0'>↑</a> <a href='confirmed_properties.php?sort_propertyType=1'>↓</a></th>";
echo "<th>Public</th>";
echo "<th>Commercial</th>";
echo "<th>ID</th>";
echo "<th>Owner<a href='confirmed_properties.php?sort_owner=0'>↑</a> <a href='confirmed_properties.php?sort_owner=1'>↓</a></th>";
echo "<th>ApprovedBy<a href='confirmed_properties.php?sort_approvedBy=0'>↑</a> <a href='confirmed_properties.php?sort_approvedBy=1'>↓</a></th>";
echo "<th>Avg. Rating<a href='confirmed_properties.php?sort_avgRating=0'>↑</a> <a href='confirmed_properties.php?sort_avgRating=1'>↓</a></th>";
echo "</tr>";
echo "<tr>";

if(isset($_GET['sort_name'])) {
    $temp_order = $_GET['sort_name'];
    if($temp_order==0) {
        $query = $query. " order by Name asc";
    } else {
        $query = $query. " order by Name desc";
    }
}

if(isset($_GET['sort_size'])) {
    $temp_order = $_GET['sort_size'];
    if($temp_order==0) {
        $query = $query. " order by Size asc";
    } else {
        $query = $query. " order by Size desc";
    }
}

if(isset($_GET['sort_owner'])) {
    $temp_order = $_GET['sort_owner'];
    if($temp_order==0) {
        $query = $query. " order by Owner asc";
    } else {
        $query = $query. " order by Owner desc";
    }
}

if(isset($_GET['sort_propertyType'])) {
    $temp_order = $_GET['sort_propertyType'];
    if($temp_order==0) {
        $query = $query. " order by PropertyType asc";
    } else {
        $query = $query. " order by PropertyType desc";
    }
}

if(isset($_GET['sort_approvedBy'])) {
    $temp_order = $_GET['sort_approvedBy'];
    if($temp_order==0) {
        $query = $query. " order by ApprovedBy asc";
    } else {
        $query = $query. " order by ApprovedBy desc";
    }
}

if(isset($_GET['sort_avgRating'])) {
    $temp_order = $_GET['sort_avgRating'];
    if($temp_order==0) {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL
                  GROUP BY p.ID
                  ORDER BY AVG(v.Rating) ASC";
    } else {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL
                  GROUP BY p.ID
                  ORDER BY AVG(v.Rating) DESC";
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
            $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                    LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                                    GROUP BY p.ID
                                    HAVING AVG(v.Rating) >= $From AND AVG(v.Rating) <= $To))";
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
        echo "<td>" .$row['Owner']. "</td>";
        echo "<td>" .$row['ApprovedBy']. "</td>";
        echo "<td>";
        $tmp_ID = $row['ID'];
        $avgRating_query = "SELECT AVG(Rating) as avgRating FROM Visit WHERE PropertyID = '$tmp_ID'";
        $avgRating_result = mysqli_query($connection, $avgRating_query);
        $tmp_row = mysqli_fetch_assoc($avgRating_result);
        if(is_numeric($tmp_row['avgRating'])){
            echo number_format($tmp_row['avgRating'], 2);
        }
        echo "</td>";
        echo "<td><a href='admin_manage_property.php?ID=".$row['ID']."'>Manage</a></td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No Confirmed Property";
}
echo "</table>";

// Search by
echo "<form action='confirmed_properties.php' method='post'>
          <select id='adminSearch' onchange='searchRange()' name='SearchBy'>
              <option value='' disabled selected>Search by</option>
              <option value='Name'>Name</option>
              <option value='Street'>Street</option>
              <option value='City'>City</option>
              <option value='Zip'>Zip</option>
              <option value='Size'>Size</option>
              <option value='PropertyType'>PropertyType</option>
              <option value='IsPublic'>Public</option>
              <option value='IsCommercial'>Commercial</option>
              <option value='ID'>ID</option>
              <option value='Owner'>Owner</option>
              <option value='ApprovedBy'>ApprovedBy</option>
              <option value='Avg. Rating'>Avg. Rating</option>
          </select><br>
          <span id='term'><input type='text' placeholder='Search Term' name='SearchTerm'><br></span>
          <input value='Search Properties' type='submit'>
    </form>";

if(isset($_SESSION["adminName"])) {
    echo "<a href='admin_menu.php?name=".$_SESSION["adminName"]."'>Back</a><br/>";
}

mysqli_close($connection);
?>

<script>
function searchRange() {
    var x = document.getElementById('adminSearch').value;
    if(x=='Avg. Rating'){
        document.getElementById('term').innerHTML = "<input type='text' placeholder='From...' name='From'> - <input type='text' placeholder='To...' name='To'><br>";
    } else {
        document.getElementById('term').innerHTML = "<input type='text' placeholder='Search Term' name='SearchTerm'><br>";
    }
}
</script>

</body>
</html>
