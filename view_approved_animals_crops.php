<html>
<body>
<?php
session_start();
require 'dbinfo.php';
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);

if(isset($_GET['Delete'])) {
    $temp_name = $_GET['Delete'];
    $delete_query = "Delete from FarmItem where Name='$temp_name'";
    mysqli_query($connection, $delete_query);
}

$query = "SELECT * FROM FarmItem WHERE IsApproved = 1";


echo "<table>";
echo "<tr>";
echo "<th>Name<a href='view_approved_animals_crops.php?sort_name=0'>↑</a> <a href='view_approved_animals_crops.php?sort_name=1'>↓</a></th>";
echo "<th>Type<a href='view_approved_animals_crops.php?sort_type=0'>↑</a> <a href='view_approved_animals_crops.php?sort_type=1'>↓</a></th>";
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

if(isset($_POST['SearchBy']) && isset($_POST['SearchTerm'])) {
    $attr = $_POST['SearchBy'];
    $SearchTerm = $_POST['SearchTerm'];
    $query .= " AND ($attr LIKE '%$SearchTerm%')";
    $result = mysqli_query($connection, $query);
}

if($result != False && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result))
    {
        echo "<td>" .$row['Name']. "</td>";
        echo "<td>" .$row['Type']. "</td>";
        echo "<td><a href='view_approved_animals_crops.php?Delete=".$row['Name']."'>Delete</a></td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No Approved Animals or Crops";
}
echo "</table>";

// Add new Animals/Crops manually by admin
if(isset($_POST['newACType']) && isset($_POST['newAC'])) {
    $newACType = $_POST['newACType'];
    $newAC = ucwords($_POST['newAC']);
    $addAC_query = "INSERT INTO FarmItem(Name, IsApproved, Type) VALUES('$newAC', 1, '$newACType')";
    mysqli_query($connection, $addAC_query);
}

echo "<form action='view_approved_animals_crops.php' method='post'>
          <select required name='newACType'>
              <option value='' disabled selected>Type...</option>
              <option value='Fruit'>Fruit</option>
              <option value='Flower'>Flower</option> 
              <option value='Vegetable'>Vegetable</option> 
              <option value='Animal'>Animal</option> 
          </select><br>
          <input type='text' placeholder='Enter Name' name='newAC'><br>
          <input value='Add to Approved List' type='submit'>
    </form>";

// Search by
echo "<form action='view_approved_animals_crops.php' method='post'>
          <select name='SearchBy'>
              <option value='' disabled selected>Search by</option>
              <option value='Name'>Name</option>
              <option value='Type'>Type</option> 
          </select><br>
          <input type='text' placeholder='Search Term' name='SearchTerm'><br>
          <input value='Search' type='submit'>
    </form>";

mysqli_close($connection);

if(isset($_SESSION["adminName"])) {
  echo "<a href='admin_menu.php?name=".$_SESSION["adminName"]."'>Back</a><br/>";
}

?>
</body>
</html>
