<html>
<body>
<?php
session_start();
require 'dbinfo.php';
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);

$query = "SELECT * FROM Property WHERE ApprovedBy IS NULL";

echo "<h2>Unconfirmed Properties</h2>";
echo "<table>";
echo "<tr>";
echo "<th>Name<a href='unconfirmed_properties.php?sort_name=0'>↑</a> <a href='unconfirmed_properties.php?sort_name=1'>↓</a></th>";
echo "<th>Street</th>";
echo "<th>City</th>";
echo "<th>Zip</th>";
echo "<th>Size<a href='unconfirmed_properties.php?sort_size=0'>↑</a> <a href='unconfirmed_properties.php?sort_size=1'>↓</a></th>";
echo "<th>PropertyType</th>";
echo "<th>Public</th>";
echo "<th>Commercial</th>";
echo "<th>ID</th>";
echo "<th>Owner<a href='unconfirmed_properties.php?sort_owner=0'>↑</a> <a href='unconfirmed_properties.php?sort_owner=1'>↓</a></th>";
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
        echo "<td>" .$row['Street']. "</td>";
        echo "<td>" .$row['City']. "</td>";
        echo "<td>" .$row['Zip']. "</td>";
        echo "<td>" .$row['Size']. "</td>";
        echo "<td>" .$row['PropertyType']. "</td>";
        echo "<td>" .$row['IsPublic']. "</td>";
        echo "<td>" .$row['IsCommercial']. "</td>";
        echo "<td>" .str_pad($row['ID'], 5, '0', STR_PAD_LEFT). "</td>";
        echo "<td>" .$row['Owner']. "</td>";
        echo "<td><a href='admin_manage_property.php?ID=".$row['ID']."'>Manage</a></td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No Unconfirmed Property";
}
echo "</table>";

// Search by
echo "<form action='unconfirmed_properties.php' method='post'>
          <select required name='SearchBy'>
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
          </select><br>
          <input type='text' placeholder='Search Term' name='SearchTerm'><br>
          <input value='Search Properties' type='submit'>
    </form>";

if(isset($_SESSION["adminName"])) {
  echo "<a href='admin_menu.php?name=".$_SESSION["adminName"]."'>Back</a><br/>";
}

mysqli_close($connection);
?>
</body>
</html>
