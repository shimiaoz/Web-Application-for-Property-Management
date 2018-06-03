<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Property Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .main {
            height: 450px;
        }
        .button-bottom {
            margin-top: 25px;
        }
    </style>
</head>
<body>

<?php
require 'dbinfo.php';
$connection = new mysqli($host, $usernameDB, $passwordDB, $database);

$Previous_Page = $_SESSION["Current_Page"];
$PropertyID = $_GET['ID'];
$username = $_SESSION["User"]["Username"];

if (isset($_POST["rate"]))
{
    $date = date('Y-m-d H:i:s');
    $rate = $_POST["rate"];
    $query_log = "INSERT INTO Visit VALUES ('$username', '$PropertyID', '$date', '$rate')";
    $connection->query($query_log);
}
elseif (isset($_POST["unlog"]))
{
    $query_unlog = "DELETE FROM Visit WHERE Username='$username' AND PropertyID='$PropertyID'";
    $connection->query($query_unlog);
}

// Get property information
$query1 = "SELECT * FROM Property WHERE ID = '$PropertyID'";
$query1_result = $connection->query($query1);
$row1 = $query1_result->fetch_assoc();

// Get visit information
$query2 = "SELECT COUNT(*) as num_visits, Avg(Rating) AS avg_rating FROM Visit WHERE PropertyID = '$PropertyID'";
$query2_result = $connection->query($query2);
$row2 = $query2_result->fetch_assoc();

// Get owner contact information
$owner = $row1["Owner"];
$query3 = "SELECT Email FROM User WHERE Username='$owner'";
$query3_result = $connection->query($query3);
$row3 = $query3_result->fetch_assoc();

// Get property items (crops/animals) information
$query4 = "SELECT Has.ItemName FROM Has INNER JOIN FarmItem on Has.ItemName=FarmItem.Name WHERE TYPE!='ANIMAL' AND PropertyID='$PropertyID'";
$query4_result = $connection->query($query4);

$query5 = "SELECT Has.ItemName FROM Has INNER JOIN FarmItem on Has.ItemName=FarmItem.Name WHERE TYPE='ANIMAL' AND PropertyID='$PropertyID'";
$query5_result = $connection->query($query5);


$format = "
<div class='main'>
    <div class='topBar'></div>
    <div class='title'>$row1[Name] Details</div>
    <div class='detail'>ID: <span class='right'>%'.05s</span></div>
    <div class='detail'>Name: <span class='right'>$row1[Name]</span></div>
    <div class='detail'>Owner: <span class='right'>$row1[Owner]</span></div>
    <div class='detail'>Contact: <span class='right'>$row3[Email]</span></div>
    <div class='detail'>Address: <span class='right'>$row1[Street], $row1[City], $row1[Zip]</span></div>
    <div class='detail'>Type: <span class='right'>%s</span></div>
    <div class='detail'>Size (acres): <span class='right'>%.1f</span></div>
    <div class='detail'>Visits: <span class='right'>$row2[num_visits]</span></div>
    <div class='detail'>Avg. Rating: <span class='right'>%.2f</span></div>
    <div class='detail'>isPublic: <span class='right'>%s</span></div>
    <div class='detail'>isCommercial: <span class='right'>%s</span></div>
    <div class='detail'>Crops: <span class='right'>
";
echo sprintf($format, $row1["ID"], ucwords(strtolower($row1["PropertyType"])), $row1["Size"], $row2["avg_rating"], boolval($row1["IsPublic"])? "True" : "False", boolval($row1["IsCommercial"])? "True" : "False");

$tmp = "";
while ($row4 = $query4_result->fetch_assoc())
{
    $tmp .= $row4["ItemName"] . ", ";
}
echo rtrim($tmp, ", ") . "</span></div>";

if ($query5_result->num_rows > 0)
{
    echo "<div class='detail'>Animals: <span class='right'>";
    $tmp = "";
    while ($row5 = $query5_result->fetch_assoc())
    {
        $tmp .= $row5["ItemName"] . ", ";
    }
    echo rtrim($tmp, ", ") . "</span></div>";
}
else
    echo "<br />";

if (strcmp($_SESSION["User"]["UserType"], "OWNER") == 0)
{
    if (strpos($Previous_Page, "owner_main_page") !== false)
        $Previous_Page .= "?name=$username";
    
    echo <<<EOT
    <div class="button-bottom">
        <input class="button" type="button" onclick="location.href='$Previous_Page';" value="Back">
    </div>
EOT;
}
elseif (strcmp($_SESSION["User"]["UserType"], "VISITOR") == 0)
{
    $query_visited = "SELECT * FROM Visit WHERE Username='$username' AND PropertyID='$PropertyID'";
    $query_visited_result = $connection->query($query_visited);

    if ($query_visited_result->num_rows == 0)
    {   // No visit record for this property
        echo <<<EOT
            <div class="detail">
                <form id="rate_form" action="property_details.php?ID=$PropertyID" method="post">
                    Rate Your Visit:
                    <select name="rate" required>
                        <option value="" disabled selected>Select</option>
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                    </select>
                </form>
            </div>
            <div class="button-bottom">
                <input class="button" type="submit" form="rate_form" value="Log Visit">
                <input class="button" type="button" onclick="location.href='$Previous_Page?name=$username';" value="Back">
            </div>
EOT;
    }
    else
    {
        echo <<<EOT
        <form id="unlog_form" action="property_details.php?ID=$PropertyID" method="post">
        </form>
        <div class="button-bottom">
            <input class="button" type="submit" form="unlog_form" name="unlog" value="Un-Log Visit">
            <input class="button" type="button" onclick="location.href='$Previous_Page?name=$username';" value="Back">
        </div>
EOT;
    }
}

echo "</div>";
$connection->close();
?>

</body>
</html>