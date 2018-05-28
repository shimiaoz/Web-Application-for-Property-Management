<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visitor Main Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    .main {
        width: 1100px;
        min-height: 450px;
    }
    </style>
</head>
<body>
<?php
require 'dbinfo.php';
$connection = new mysqli($host, $usernameDB, $passwordDB, $database);
$query = "SELECT * FROM Property WHERE ApprovedBy IS NOT NULL AND IsPublic = 1";

$username = $_GET['name'];
echo <<<EOT
<div class="main">
    <div class="topBar"></div>
    <div class="title">Welcome $username</div>
    <div class="table-div">
        <table id="table">
            <caption>All Public, Confirmed Properties</caption>
            <thead>
                <tr>
                    <th>ID<span id="sort_id" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_id', 0)"></i></span></th>
                    <th>Name<span id="sort_name" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_name', 1)"></i></span></th>
                    <th>Street<span id="sort_street" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_street', 2)"></i></span></th>
                    <th>City<span id="sort_city" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_city', 3)"></i></span></th>
                    <th>Zip<span id="sort_zip" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_zip', 4)"></i></span></th>
                    <th>Size<span id="sort_size" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_size', 5)"></i></span></th>
                    <th>Property Type<span id="sort_propertyType" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_propertyType', 6)"></i></span></th>
                    <th>isPublic<span id="sort_public" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_public', 7)"></i></span></th>
                    <th>isCommercial<span id="sort_commercial" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_commercial', 8)"></i></span></th>
                    <th>Visits<span id="sort_visits" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_visits', 9)"></i></span></th>
                    <th>Rating<span id="sort_rating" class="sort-icon"><i class="fa fa-chevron-circle-down" onclick="sortTable('sort_rating', 10)"></i></span></th>
                </tr>
            </thead>
EOT;

$result = $connection->query($query);

if (isset($_POST["SearchBy"]))
{
    $attr = $_POST["SearchBy"];
    if (isset($_POST["SearchTerm"]))
    {
        $SearchTerm = $_POST["SearchTerm"];
        $query .= " AND ($attr LIKE '%$SearchTerm%')";
        $result = $connection->query($query);
    }
    elseif (isset($_POST["From"]) && isset($_POST["To"]))
    {
        $From = $_POST["From"];
        $To = $_POST["To"];
        if (is_numeric($From) && is_numeric($To))
        {
            if ($attr == "Size")
                $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                               WHERE p.Size >= $From AND p.Size <= $To))";
            elseif ($attr == "Visits")
                $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                               LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                                               GROUP BY p.ID 
                                               HAVING COUNT(v.PropertyID) >= $From AND COUNT(v.PropertyID) <= $To))";
            elseif ($attr == "Rating")
                $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                               LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                                               GROUP BY p.ID
                                               HAVING AVG(v.Rating) >= $From AND AVG(v.Rating) <= $To))";
            $result = $connection->query($query);
        }
    }
}

if ($result != False && $result->num_rows > 0)
{
    echo "<tbody>";
    while($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        $tmp_ID = $row['ID'];
        $check_log_query = "SELECT * FROM Visit WHERE Username = '$username' AND PropertyID = $tmp_ID";
        $check_log_result = $connection->query($check_log_query);
        if ($check_log_result->num_rows == 0)
            echo "<a href='visitor_property_page.php?username=$username&ID=$tmp_ID'>" . str_pad($row['ID'], 5, '0', STR_PAD_LEFT) . "</a>";
        else
            echo "<a href='visitor_property_logged.php?username=$username&ID=$tmp_ID'>" . str_pad($row['ID'], 5, '0', STR_PAD_LEFT) . "</a>";
        echo "</td>";
        echo "<td>" .$row['Name']. "</td>";
        echo "<td>" .$row['Street']. "</td>";
        echo "<td>" .$row['City']. "</td>";
        echo "<td>" .$row['Zip']. "</td>";
        echo "<td>" .number_format($row['Size'], 1). "</td>";
        echo "<td>" .ucwords(strtolower($row['PropertyType'])). "</td>";
        if ($row['IsPublic'] == "1")
            echo "<td>True</td>";
        else
            echo "<td>False</td>";
        if ($row['IsCommercial'] == "1")
            echo "<td>True</td>";
        else
            echo "<td>False</td>";
        echo "<td>";
        $num_visits_query = "SELECT COUNT(*) as num_visits FROM Visit WHERE PropertyID = $tmp_ID";
        $num_visits_result = $connection->query($num_visits_query);
        $tmp_row = $num_visits_result->fetch_assoc();
        echo $tmp_row['num_visits'];
        echo "</td>";
        echo "<td>";
        $avgRating_query = "SELECT AVG(Rating) as avgRating FROM Visit WHERE PropertyID = $tmp_ID";
        $avgRating_result = $connection->query($avgRating_query);
        $tmp_row = $avgRating_result->fetch_assoc();
        if (is_numeric($tmp_row['avgRating'])){
            echo number_format($tmp_row['avgRating'], 2);
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
}
echo "</table>";
echo "</div>";

echo "<div id='bottom-fields'>";
// Search by
echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <form action="visitor_main_page.php?name=$username" method="post">
                <div>
                    <select id="visitorSearch" onchange="searchRange()" name="SearchBy">
                        <option value="" disabled selected>Search by</option>
                        <option value="ID">ID</option>
                        <option value="Name">Name</option>
                        <option value="Street">Street</option>
                        <option value="City">City</option>
                        <option value="Zip">Zip</option>
                        <option value="Size">Size</option>
                        <option value="PropertyType">Property Type</option>
                        <option value="Visits">Visits</option>
                        <option value="Rating">Rating</option>
                    </select>
                </div>
                <div id="term-input">
                    <input class="term" type="text" placeholder="Search Term" name="SearchTerm">
                </div>
                <div>
                    <input class="button" value="Search Properties" type="submit">
                </div>
            </form>
        </div>
    </div>
EOT;

echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <div>
                <input class="button" type="button" onclick="location.href='visitor_history.php?name=$username';" value="View Visit History">
            </div>
        </div>
    </div>
EOT;

echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <input class="button field" type="button" onclick="location.href='login.php';" value="Log Out">
        </div>
    </div>
EOT;
echo "</div>";

echo "</div>"; // For div "main"

$connection->close();
?>
<script src="helperFunctions.js"></script>
</body>
</html>
