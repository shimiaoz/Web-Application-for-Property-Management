<?php
// Start the session
session_start();
require 'dbinfo.php' ; 
?>
<html>
<title>Other property</title>
<body>


<?php
$email = $_GET['email'];
//echo "this is your email: '$email'. ";
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
        
$tmp = mysqli_query($connection, "SELECT * FROM User WHERE Email = '$email'");
while($row = mysqli_fetch_array($tmp)){
    $username = $row['Username'];
    //echo "Got username: '$username'";
}
//(ID, Name, Size, IsCommercial, IsPublic, Street, City, Zip, ProprtyType, Owner, ApprovedBy)
$query = "SELECT * FROM Property WHERE Owner != '$username'";
if(isset($_GET['sort_name'])) {
    $temp_order = $_GET['sort_name'];
    if($temp_order==0) {
        $query = $query. " ORDER BY Name asc";
    } else {
        $query = $query. " order by Name desc";
    }
}else if(isset($_GET['sort_city'])) {
    $temp_order = $_GET['sort_city'];
    if($temp_order==0) {
        $query = $query. " order by City asc";
    } else {
        $query = $query. " order by City desc";
    }
}else if(isset($_GET['sort_type'])) {
    $temp_order = $_GET['sort_type'];
    if($temp_order==0) {
        $query = $query. " order by PropertyType asc";
    } else {
        $query = $query. " order by PropertyType desc";
    }
}else if(isset($_GET['sort_public'])) {
    $temp_order = $_GET['sort_public'];
    if($temp_order==0) {
        $query = $query. " order by IsPublic asc";
    } else {
        $query = $query. " order by IsPublic desc";
    }
}

if(isset($_GET['sort_visits'])) {
    $temp_order = $_GET['sort_visits'];
    if($temp_order==0) {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE Owner != '$username'
                  GROUP BY p.ID
                  ORDER BY count(v.PropertyID) ASC";
    } else {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE Owner != '$username'
                  GROUP BY p.ID
                  ORDER BY count(v.PropertyID) DESC";
    }
}

if(isset($_GET['sort_avgRating'])) {
    $temp_order = $_GET['sort_avgRating'];
    if($temp_order==0) {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE Owner != '$username'
                  GROUP BY p.ID
                  ORDER BY AVG(Rating) ASC";
    } else {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE Owner != '$username'
                  GROUP BY p.ID
                  ORDER BY AVG(Rating) DESC";
    }
}

if(isset($_POST['keyword']) & isset($_POST['search_type'])){
    $keyword = $_POST['keyword'];
    $search_type = $_POST['search_type'];
}
if(isset($_POST['sid'])){
    $sid = $_POST['sid'];
}
if(isset($_POST['detail'])){
    header("Location: property_details.php/?ID=$sid");
    exit;
}

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


echo "<h3>All Other Valid Properties</h3>";

echo "<table border='1'>
  <tr>
    <th>ID</th>
    <th>Name<a href='?sort_name=0&&email=$email'>↑</a> <a href='?sort_name=1&&email=$email'>↓</a></th>
    <th>Size</th>
    <th>IsCommercial?</th>
    <th>IsPublic?<a href='?sort_public=0&&email=$email'>↑</a> <a href='?sort_public=1&&email=$email'>↓</a></th>
    <th>Street</th>
    <th>City<a href='?sort_city=0&&email=$email'>↑</a> <a href='?sort_city=1&&email=$email'>↓</a></th>
    <th>Zip</th>
    <th>Property Type<a href='?sort_type=0&&email=$email'>↑</a> <a href='?sort_type=1&&email=$email'>↓</a></th>
    <th>Owner</th>
    <th>Visits<a href='?sort_visits=0&&email=$email'>↑</a> <a href='?sort_visits=1&&email=$email'>↓</a></th>
    <th>Avg. Rating<a href='?sort_avgRating=0&&email=$email'>↑</a> <a href='?sort_avgRating=1&&email=$email'>↓</a></th>
  </tr>";
?>


<?php 
$result = mysqli_query($connection, $query);
while($row = mysqli_fetch_array($result)){
    ?>
    <tr>
        <td><?php echo str_pad($row['ID'], 5, '0', STR_PAD_LEFT) ?></td>
        <td><?php echo $row['Name'] ?></td>
        <td><?php echo $row['Size'] ?></td>
        <td><?php echo $row['IsCommercial'] ?></td>
        <td><?php echo $row['IsPublic'] ?></td>
        <td><?php echo $row['Street'] ?></td>
        <td><?php echo $row['City'] ?></td>
        <td><?php echo $row['Zip'] ?></td>
        <td><?php echo $row['PropertyType'] ?></td>
        <td><?php echo $row['Owner'] ?></td>
        <?php
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
        ?>
    </tr>
    <br /> 

  <?php 
}
mysqli_close($connection);
?>
</table>

<form  action='http://localhost/phase3/view_others.php?email=<?php echo $_GET['email'];?>' method='post'>
View Detail: <input type='text' name='sid'><br>
<input type='submit' name='detail' value='View Detail' /><br><br>
Search by:      <select id='ownerSearchProperty' onchange='searchRange()' name='SearchBy'>
                    <option value="" disabled selected>Select</option>
                    <?php
                    echo "<option value='Name'>Name</option>";
                    echo "<option value='City'>City</option>";
                    echo "<option value='IsPublic'>IsPublic</option>";
                    echo "<option value='Visits'>Visits</option>";
                    echo "<option value='Avg. Rating'>Avg. Rating</option>";
                    ?>
                </select><br>
Keyword: <span id='term'><input type='text' placeholder='Search Term' name='SearchTerm'></span><br>
<input type='submit' name='search' value='search properties' />
</form>

<?php
echo "<a href='http://localhost/phase3/owner_function.php/?email=$email'>Back</a><br/>";
?>

<script>
function searchRange() {
    var x = document.getElementById('ownerSearchProperty').value;
    if(x=='Visits' || x=='Avg. Rating'){
        document.getElementById('term').innerHTML = "<input type='text' placeholder='From' name='From'> - <input type='text' placeholder='To' name='To'>";
    } else {
        document.getElementById('term').innerHTML = "<input type='text' placeholder='Search Term' name='SearchTerm'>";
    }
}
</script>

</body>
</html>