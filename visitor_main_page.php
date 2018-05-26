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
    <div class="table">
        <table>
            <caption>All Public, Confirmed Properties</caption>
            <thead>
                <tr>
                    <th>Name<a href='visitor_main_page.php?name=$username&sort_by_name=0'><i class="fa fa-chevron-circle-up"></i></a>
                            <a href='visitor_main_page.php?name=$username&sort_by_name=1'><i class="fa fa-chevron-circle-down"></i></a></th>
                    <th>Street</th>
                    <th>City<a href='visitor_main_page.php?name=$username&sort_by_city=0'></span></a>
                            <a href='visitor_main_page.php?name=$username&sort_by_city=1'></a></th>
                    <th>Zip</th>
                    <th>Size</th>
                    <th>Property Type<a href='visitor_main_page.php?name=$username&sort_by_propertyType=0'></a>
                                    <a href='visitor_main_page.php?name=$username&sort_by_propertyType=1'></a></th>
                    <th>isPublic</th>
                    <th>isCommercial</th>
                    <th>ID</th>
                    <th>Visits<a href='visitor_main_page.php?name=$username&sort_by_visits=0'>↑</a>
                              <a href='visitor_main_page.php?name=$username&sort_by_visits=1'>↓</a></th>
                    <th>Avg. Rating<a href='visitor_main_page.php?name=$username&sort_by_avgRating=0'>↑</a>
                                   <a href='visitor_main_page.php?name=$username&sort_by_avgRating=1'>↓</a></th>
                </tr>
            </thead>
EOT;

if (isset($_GET['sort_by_name']))
{
    $temp_order = $_GET['sort_by_name'];
    if ($temp_order == 0)
        $query .= " order by Name asc";
    else
        $query .= " order by Name desc";
}

if (isset($_GET['sort_by_city']))
{
    $temp_order = $_GET['sort_by_city'];
    if ($temp_order == 0)
        $query .= " order by City asc";
    else
        $query .= " order by City desc";
}

if (isset($_GET['sort_by_propertyType']))
{
    $temp_order = $_GET['sort_by_propertyType'];
    if ($temp_order == 0)
        $query .= " order by PropertyType asc";
    else
        $query .= " order by PropertyType desc";
}

if (isset($_GET['sort_by_visits']))
{
    $temp_order = $_GET['sort_by_visits'];
    if ($temp_order == 0)
    {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY count(v.PropertyID) ASC";
    }
    else
    {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY count(v.PropertyID) DESC";
    }
}

if (isset($_GET['sort_by_avgRating']))
{
    $temp_order = $_GET['sort_by_avgRating'];
    if ($temp_order == 0)
    {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY AVG(Rating) ASC";
    }
    else
    {
        $query = "SELECT p.* FROM Property AS p
                  LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                  WHERE p.ApprovedBy IS NOT NULL AND p.IsPublic = 1
                  GROUP BY p.ID
                  ORDER BY AVG(Rating) DESC";
    }
}

$result = $connection->query($query);

if (isset($_POST['SearchBy']))
{
    $attr = $_POST['SearchBy'];
    if (isset($_POST['SearchTerm']))
    {
        $SearchTerm = $_POST['SearchTerm'];
        $query .= " AND ($attr LIKE '%$SearchTerm%')";
        $result = $connection->query($query);
    }
    elseif (isset($_POST['From']) && isset($_POST['To']))
    {
        $From = $_POST['From'];
        $To = $_POST['To'];
        if (is_numeric($From) && is_numeric($To))
        {
            if ($attr=='Visits')
                $query .= " AND (ID IN (SELECT p.ID FROM Property as p
                                               LEFT JOIN Visit AS v ON p.ID = v.PropertyID
                                               GROUP BY p.ID 
                                               HAVING COUNT(v.PropertyID) >= $From AND COUNT(v.PropertyID) <= $To))";
            elseif ($attr == 'Avg. Rating')
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
        echo "<td>" .str_pad($row['ID'], 5, '0', STR_PAD_LEFT). "</td>";
        echo "<td>";
        $tmp_ID = $row['ID'];
        $num_visits_query = "SELECT COUNT(*) as num_visits FROM Visit WHERE PropertyID = '$tmp_ID'";
        $num_visits_result = $connection->query($num_visits_query);
        $tmp_row = $num_visits_result->fetch_assoc();
        echo $tmp_row['num_visits'];
        echo "</td>";
        echo "<td>";
        $avgRating_query = "SELECT AVG(Rating) as avgRating FROM Visit WHERE PropertyID = '$tmp_ID'";
        $avgRating_result = $connection->query($avgRating_query);
        $tmp_row = $avgRating_result->fetch_assoc();
        if (is_numeric($tmp_row['avgRating'])){
            echo number_format($tmp_row['avgRating'], 2);
        }
        echo "</td>";
        echo "<td>";
        $check_log_query = "SELECT * FROM Visit WHERE Username='$username' AND PropertyID='$tmp_ID'";
        $check_log_result = $connection->query($check_log_query);
        if ($check_log_result->num_rows == 0)
            echo "<a href='visitor_property_page.php?username=$username&ID=".$row['ID']."'>View Property</a>";
        else
            echo "<a href='visitor_property_logged.php?username=$username&ID=".$row['ID']."'>View Property</a>";
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
                        <option value="Name">Name</option>
                        <option value="Street">Street</option>
                        <option value="City">City</option>
                        <option value="Zip">Zip</option>
                        <option value="Size">Size</option>
                        <option value="PropertyType">Property Type</option>
                        <option value="ID">ID</option>
                        <option value="Visits">Visits</option>
                        <option value="Avg. Rating">Avg. Rating</option>
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
                <input class="button" type="button" onclick="location.href='login.php';" value="View Property">
            </div>
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

<script>
function searchRange() {
    var x = document.getElementById('visitorSearch').value;
    if (x=='Visits' || x=='Avg. Rating') {
        document.getElementById('term-input').innerHTML = "<input class='term range' type='number' step='any' placeholder='From' name='From' required> - <input class='term range' type='number' step='any' placeholder='To' name='To' required>";
    } else {
        document.getElementById('term-input').innerHTML = "<input class='term' type='text' placeholder='Search Term' name='SearchTerm'>";
    }
}
</script>

</body>
</html>
