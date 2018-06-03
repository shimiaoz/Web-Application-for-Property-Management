<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visit History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .button-bottom {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php
require 'dbinfo.php';

$_SESSION["Current_Page"] = htmlspecialchars($_SERVER["PHP_SELF"]);

echo <<<EOT
<div class="main">
    <div class="topBar"></div>
    <div class="title">Your Visit History</div>
    <div class="table-div">
        <table id="table">
            <thead>
                <tr>
                    <th>Name<span id="sort_name" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_name', 0)"></i></span></th>
                    <th>Date Logged<span id="sort_date" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_date', 1)"></i></span></th>
                    <th>Rating<span id="sort_rating" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_rating', 2)"></i></span></th>
                </tr>
            </thead>
EOT;

$connection = new mysqli($host, $usernameDB, $passwordDB, $database);
$username = $_GET['name'];
$query = "SELECT Property.ID, Property.Name, Visit.VisitDate, Visit.Rating FROM Visit LEFT JOIN Property ON Property.ID = Visit.PropertyID WHERE Visit.Username='$username'";
$result = $connection->query($query);

if($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        echo "<tr>";
        $name = $row['Name'];
        $ID = $row['ID'];
        echo "<td><a href='property_details.php?ID=$ID'>$name</a></td>";
        echo "<td>" . $row['VisitDate']. "</td>";
        echo "<td>" . $row['Rating'] . "</td>";
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";

echo <<<EOT
    <div class="button-bottom">
        <input class="button" type="button" onclick="location.href='visitor_main_page.php?name=$username';" value="Back">
    </div>
EOT;
echo "</div>";

$connection->close();
?>
<script src="helperFunctions.js"></script>
</body>
</html>