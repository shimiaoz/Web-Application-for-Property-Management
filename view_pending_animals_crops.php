<html>
<body>
<?php
session_start();
require 'dbinfo.php';
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);
if(isset($_GET['Approve'])) {
    $temp_name = $_GET['Approve'];
    $approve_query = "Update FarmItem Set IsApproved=1 where Name='$temp_name'";
    mysqli_query($connection, $approve_query);
}

if(isset($_GET['Delete'])) {
    $temp_name = $_GET['Delete'];
    $delete_query = "Delete from FarmItem where Name='$temp_name'";
    mysqli_query($connection, $delete_query);
}

$query = "SELECT * FROM FarmItem WHERE IsApproved = 0";


echo "<table>";
echo "<tr>";
echo "<th>Name<a href='view_pending_animals_crops.php?sort_name=0'>↑</a> <a href='view_pending_animals_crops.php?sort_name=1'>↓</a></th>";
echo "<th>Type<a href='view_pending_animals_crops.php?sort_type=0'>↑</a> <a href='view_pending_animals_crops.php?sort_type=1'>↓</a></th>";
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

if(isset($_GET['sort_type'])) {
    $temp_order = $_GET['sort_type'];
    if($temp_order==0) {
        $query = $query. " order by Type asc";
    } else {
        $query = $query. " order by Type desc";
    }
}

$result = mysqli_query($connection, $query);

if($result != False && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result))
    {
        echo "<td>" .$row['Name']. "</td>";
        echo "<td>" .$row['Type']. "</td>";
        echo "<td><a href='view_pending_animals_crops.php?Approve=".$row['Name']."'>Approve</a></td>";
        echo "<td><a href='view_pending_animals_crops.php?Delete=".$row['Name']."'>Delete</a></td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No Pending Animals or Crops";
}
echo "</table>";
mysqli_close($connection);

if(isset($_SESSION["adminName"])) {
  echo "<a href='admin_menu.php?name=".$_SESSION["adminName"]."'>Back</a><br/>";
}
?>

</body>
</html>
