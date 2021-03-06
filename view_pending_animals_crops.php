<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Approval Animals/Crops</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .main {
            height: 450px;
        }

        .col-33 .fields .button,
        .col-33 .fields select,
        .col-33 .fields .term {
            width: 90%;
        }

        table {
            width: 60%;
        }
    </style>
    <script src="helperFunctions.js"></script>
</head>
<body>
<?php
require 'dbinfo.php';

$connection = new mysqli($host, $usernameDB, $passwordDB, $database);
$query = "SELECT * FROM FarmItem WHERE IsApproved = 0";

if (isset($_POST['SearchBy']) && isset($_POST['SearchTerm'])) {
    $attr = $_POST['SearchBy'];
    $SearchTerm = $_POST['SearchTerm'];
    $query .= " AND ($attr LIKE '%$SearchTerm%')";
}

if (isset($_POST['item_to_approve']))
{
    $temp_name = $_POST['item_to_approve'];
    $approve_query = "Update FarmItem Set IsApproved=1 where Name='$temp_name'";
    $connection->query($approve_query);
}

if (isset($_POST['item_to_delete']))
{
    $temp_name = $_POST['item_to_delete'];
    $delete_query = "Delete from FarmItem where Name='$temp_name'";
    $connection->query($delete_query);
}

echo <<<EOT
<div class="main">
    <div class="topBar"></div>
    <div class="title">Pending Approval Animals/Crops</div>
    <div class="table-div">
        <table id="table">
            <thead>
                <tr>
                    <th>Name<span id="sort_name" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_name', 0)"></i></span></th>
                    <th>Type<span id="sort_type" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_type', 1)"></i></span></th>
                </tr>
            </thead>
EOT;

$result = $connection->query($query);

if ($result->num_rows > 0)
{
    echo "<tbody>";
    while($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>" .$row['Name']. "</td>";
        echo "<td>" .ucfirst(strtolower($row['Type'])). "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
}
echo "</table>";
echo "</div>";

echo "<div id='bottom-fields'>";
echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <div>
                <form action='view_pending_animals_crops.php' method="post">
                    <div>
                        <select name='SearchBy'>
                            <option value='' disabled selected>Search by</option>
                            <option value='Name'>Name</option>
                            <option value='Type'>Type</option> 
                        </select>
                    </div>
                    <div>
                        <input class="term" type="text" placeholder="Search Term" name="SearchTerm">
                    </div>
                    <div>
                        <input class="button" value="Search" type="submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
EOT;

echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <div>
                <form action="view_pending_animals_crops.php" method="post">
                    <input type="hidden" id="item_to_approve" name="item_to_approve" value="">
                    <input class="button" type="Submit" value="Approve Selection">
                </form>
                <form action="view_pending_animals_crops.php" method="post">
                    <input type="hidden" id="item_to_delete" name="item_to_delete" value="">
                    <input class="button" type="Submit" value="Delete Selection">
                </form>
                <input class="button" type="button" onclick="location.href='admin_menu.php';" value="Back">
            </div>
        </div>
    </div>
EOT;
echo "</div>";
echo "</div>"; // For div "main"

$connection->close();
?>

<script>selectRow(["item_to_approve", "item_to_delete"]);</script>

</body>
</html>