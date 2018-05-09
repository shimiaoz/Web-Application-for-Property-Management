<html>
<body>
<?php
session_start();
require 'dbinfo.php';

$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);
if(isset($_GET['PropertyName'])) {
    $P_Name = $_GET['PropertyName'];
    $query = "SELECT ID from Property WHERE Name='$P_Name'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    $ID = $row['ID'];
    header("Location: property_details.php/?ID=$ID");
}

if(isset($_GET['name'])){
    $username = $_GET['name'];
}
$query = "SELECT Property.Name, Visit.VisitDate, Visit.Rating FROM Visit LEFT JOIN Property ON Property.ID=Visit.PropertyID WHERE Visit.Username='$username'";
echo "<table>";
echo "<tr>";
echo "<th>Name <a href='visitor_history.php?name=$username&sort_name=0'>↑</a> <a href='visitor_history.php?name=$username&sort_name=1'>↓</a></th>";
echo "<th>Date Logged<a href='visitor_history.php?name=$username&sort_date=0'>↑</a> <a href='visitor_history.php?name=$username&sort_date=1'>↓</a></th>";
echo "<th>Rating<a href='visitor_history.php?name=$username&sort_rate=0'>↑</a> <a href='visitor_history.php?name=$username&sort_rate=1'>↓</a> </th>";
echo "</tr>";
echo "<tr>";
if(isset($_GET['sort_name'])) {
    $temp_order = $_GET['sort_name'];
    if($temp_order==0) {
        $query = $query. " order by Property.Name asc";
    } else {
        $query = $query. " order by Property.Name desc";
    }
}
if(isset($_GET['sort_date'])) {
    $temp_order = $_GET['sort_date'];
    if($temp_order==0) {
        $query = $query. " order by Visit.VisitDate asc";
    } else {
        $query = $query. " order by Visit.VisitDate desc";
    }
}
if(isset($_GET['sort_rate'])) {
    $temp_order = $_GET['sort_rate'];
    if($temp_order==0) {
        $query = $query. " order by Visit.Rating asc";
    } else {
        $query = $query. " order by Visit.Rating desc";
    }
}
$result = mysqli_query($connection, $query);
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result))
    {
        $name = $row['Name'];
        echo "<td><a href='visitor_history.php?name=$username&PropertyName=".$name."'>$name</a></td>";
        echo "<td>";
        echo $row['VisitDate'];
        echo "</td>";
        echo "<td>";
        echo $row['Rating'];
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No visited history";
}
echo "</table>";

if(isset($_GET['name'])){
    $username = $_GET['name'];
    echo "<a href='visitor_main_page.php?name=$username'>Back</a><br/>";
}

mysqli_close($connection);
?>

</body>
</html>