<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Visitors</title>
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

        .col-33 .fields .range {
            width: 41.6%;
        }
    </style>
    <script src="helperFunctions.js"></script>
</head>
<body>
<?php
require 'dbinfo.php';

$connection = new mysqli($host, $usernameDB, $passwordDB, $database);
$query = "SELECT * FROM User WHERE UserType='VISITOR'";

if (isset($_POST['visitor_to_delete']))
{
    $temp_username = $_POST['visitor_to_delete'];
    $delete_query = "Delete from User where Username='$temp_username'";
    $connection->query($delete_query);
}

if (isset($_POST['log_to_delete']))
{
    $temp_username = $_POST['log_to_delete'];
    $delete_query = "Delete from Visit where Username='$temp_username'";
    $connection->query($delete_query);
}

if (isset($_POST['SearchBy']))
{
    $attr = $_POST['SearchBy'];
    if (isset($_POST['SearchTerm']))
    {
        $SearchTerm = $_POST['SearchTerm'];
        $query .= " AND ($attr LIKE '%$SearchTerm%')";
    }
    elseif (isset($_POST['From']) && isset($_POST['To']))
    {
        $From = $_POST['From'];
        $To = $_POST['To'];
        if(is_numeric($From) && is_numeric($To))
        {
            if ($attr == 'Visits')
            {
                $query .= " AND (Username IN (SELECT u.Username FROM User as u
                                               LEFT JOIN Visit AS v ON u.Username = v.Username
                                               GROUP BY u.Username
                                               HAVING COUNT(v.Username) >= $From AND COUNT(v.Username) <= $To))";
            }
        }
    }
}

echo <<<EOT
<div class="main">
    <div class="topBar"></div>
    <div class="title">All Visitors In System</div>
    <div class="table-div">
        <table id="table">
            <thead>
                <tr>
                    <th>Username<span id="sort_name" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_name', 0)"></i></span></th>
                    <th>Email<span id="sort_email" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_email', 1)"></i></span></th>
                    <th>Visits<span id="sort_visits" class="sort-icon"><i class="fa fa-chevron-circle-down" title="Asc" onclick="sortTable('sort_visits', 2)"></i></span></th>
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
        $temp_user = $row['Username'];
        $count_query = "select count(Username) as visit_number from Visit where Username='$temp_user'";
        $count_result = $connection->query($count_query);
        $data = $count_result->fetch_assoc();
        echo "<td>" . $temp_user . "</td>";
        echo "<td>" . $row['Email'] . "</td>";
        echo "<td>" . $data['visit_number'] . "</td>";
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
            <form action="view_visitors.php" method="post">
                <div>
                    <select id="adminSearchVisitor" onchange="searchRange('adminSearchVisitor')" name="SearchBy">
                        <option value="" disabled selected>Search by</option>
                        <option value="Username">Username</option>
                        <option value="Email">Email</option>
                        <option value="Visits">Visits</option>
                    </select>
                </div>
                <div id="term-input">
                    <input class="term" type="text" placeholder="Search Term" name="SearchTerm">
                </div>
                <div>
                    <input class="button" value="Search Visitors" type="submit">
                </div>
            </form>
        </div>
    </div>
EOT;

echo <<<EOT
    <div class="col-33">
        <div class="fields">
            <div>
                <form action="view_visitors.php" method="post">
                    <input type="hidden" id="visitor_to_delete" name="visitor_to_delete" value="">
                    <input class="button" type="Submit" value="Delete Visitor">
                </form>
                <form action="view_visitors.php" method="post">
                    <input type="hidden" id="log_to_delete" name="log_to_delete" value="">
                    <input class="button" type="submit" value="Delete Log History">
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

<script>selectRow(["visitor_to_delete", "log_to_delete"]);</script>

</body>
</html>