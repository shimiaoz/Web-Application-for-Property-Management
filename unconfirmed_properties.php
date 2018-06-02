<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirmed Properties</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .main {
            width: 1100px;
            min-height: 450px;
        }

        .tooltip .tooltiptext {
            width: 65px;
            top: 0px;
        }
    </style>
    <script src="helperFunctions.js"></script>
</head>
<body>
<?php
require 'dbinfo.php';

$connection = new mysqli($host, $usernameDB, $passwordDB, $database);
$query = "SELECT * FROM Property WHERE ApprovedBy IS NULL";

echo <<<EOT
<div class="main">
    <div class="topBar"></div>
    <div class="title">All Unconfirmed Properties</div>
    <div class="table-div">
        <table id="table">
            <thead>
                <tr>
                    <th>ID<span id="sort_id" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_id', 0)"></i></span></th>
                    <th>Name<span id="sort_name" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_name', 1)"></i></span></th>
                    <th>Street<span id="sort_street" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_street', 2)"></i></span></th>
                    <th>City<span id="sort_city" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_city', 3)"></i></span></th>
                    <th>Zip<span id="sort_zip" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_zip', 4)"></i></span></th>
                    <th>Size<span id="sort_size" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_size', 5)"></i></span></th>
                    <th>Property Type<span id="sort_propertyType" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_propertyType', 6)"></i></span></th>
                    <th>isPublic<span id="sort_public" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_public', 7)"></i></span></th>
                    <th>isCommercial<span id="sort_commercial" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_commercial', 8)"></i></span></th>
                    <th>Owner<span id="sort_owner" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_owner', 9)"></i></span></th>
                </tr>
            </thead>
EOT;

if (isset($_POST["SearchBy"]))
{
    $attr = $_POST["SearchBy"];
    if (isset($_POST["SearchTerm"]))
    {
        $SearchTerm = $_POST["SearchTerm"];
        $query .= " AND ($attr LIKE '%$SearchTerm%')";
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
        }
    }
}

$result = $connection->query($query);

if ($result != False && $result->num_rows > 0)
{
    echo "<tbody>";
    while($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        $tmp_ID = str_pad($row['ID'], 5, '0', STR_PAD_LEFT);
        echo "<a href='admin_manage_property.php?ID=$tmp_ID'>" . $tmp_ID . "</a>";
        echo "</td>";
        echo "<td>" .$row['Name']. "</td>";
        echo "<td>" .$row['Street']. "</td>";
        echo "<td>" .$row['City']. "</td>";
        echo "<td>" .$row['Zip']. "</td>";
        echo "<td>" .number_format($row['Size'], 1). "</td>";
        echo "<td>" .ucwords(strtolower($row['PropertyType'])). "</td>";
        echo "<td>" .(boolval($row["IsPublic"])? "True" : "False"). "</td>";
        echo "<td>" .(boolval($row["IsCommercial"])? "True" : "False"). "</td>";
        echo "<td>" .$row['Owner']. "</td>";
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
            <form action="unconfirmed_properties.php" method="post">
                <div>
                    <select id="adminSearch" onchange="searchRange('adminSearch')" name="SearchBy">
                        <option value="" disabled selected>Search by</option>
                        <option value="ID">ID</option>
                        <option value="Name">Name</option>
                        <option value="Street">Street</option>
                        <option value="City">City</option>
                        <option value="Zip">Zip</option>
                        <option value="Size">Size</option>
                        <option value="PropertyType">Property Type</option>
                        <option value="Owner">Owner</option>
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
                <form action="admin_manage_property.php?ID=" id="unconfirmed_property_form" method="post">
                    <input id="invisible_field" type="text" class="button" placeholder="Select a property" required>
                    <input id="manage" class="button" type="Submit" value="Manage Selected Property">
                    <div class="tooltip">
                        <i class='fa fa-info-circle'></i>
                        <span class="tooltiptext">Click a row</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
EOT;

echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <input class="button" type="button" onclick="location.href='admin_menu.php';" value="Back">
        </div>
    </div>
EOT;
echo "</div>";

echo "</div>"; // For div "main"

$connection->close();
?>

<script>selectRow(["unconfirmed_property_form"], "action");</script>

</body>
</html>
