<html>
<body>
<?php
session_start();
require 'dbinfo.php';

echo "<h1>All Owners In System</h1>";

$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);
if(isset($_GET['delete_owner'])) {
    $temp_username = $_GET['delete_owner'];
    $delete_query = "Delete from User where Username='$temp_username'";
    mysqli_query($connection, $delete_query);
}
$query = "SELECT * FROM User WHERE UserType='OWNER'";
echo "<table>";
echo "<tr>";
echo "<th>Username <a href='view_owner.php?sort_name=0'>↑</a> <a href='view_owner.php?sort_name=1'>↓</a></th>";
echo "<th>Email<a href='view_owner.php?sort_email=0'>↑</a> <a href='view_owner.php?sort_email=1'>↓</a></th>";
echo "<th>Number of Properties<a href='view_owner.php?sort_number=0'>↑</a> <a href='view_owner.php?sort_number=1'>↓</a></th>";
echo "</tr>";
echo "<tr>";
if(isset($_GET['sort_name'])) {
    $temp_order = $_GET['sort_name'];
    if($temp_order==0) {
        $query = $query. "order by username asc";
    } else {
        $query = $query. "order by username desc";
    }
}
if(isset($_GET['sort_email'])) {
    $temp_order = $_GET['sort_email'];
    if($temp_order==0) {
        $query = $query. "order by email asc";
    } else {
        $query = $query. "order by email desc";
    }
}
if(isset($_GET['sort_number'])) {
    $temp_order = $_GET['sort_number'];
    if($temp_order==0) {
        $query = "SELECT u.* FROM User AS u
                  LEFT JOIN Property AS v ON u.Username = v.Owner
                  WHERE u.UserType='OWNER'
                  GROUP BY u.Username
                  ORDER BY COUNT(v.Owner) ASC";
    } else {
        $query = "SELECT u.* FROM User AS u
                  LEFT JOIN Property AS v ON u.Username = v.Owner
                  WHERE u.UserType='OWNER'
                  GROUP BY u.Username
                  ORDER BY COUNT(v.Owner) DESC";
    }
}

if(isset($_POST['SearchBy'])) {
    $attr = $_POST['SearchBy'];
    if(isset($_POST['SearchTerm'])){
        $SearchTerm = $_POST['SearchTerm'];
        $query .= " AND ($attr LIKE '%$SearchTerm%')";
        $result = mysqli_query($connection, $query);
    } elseif(isset($_POST['From']) && isset($_POST['To'])) {
        $From = $_POST['From'];
        $To = $_POST['To'];
        if(is_numeric($From) && is_numeric($To)) {
            if($attr=='numProperty'){
                $query .= " AND (Username IN (SELECT u.Username FROM User as u
                                               LEFT JOIN Property AS p ON u.Username = p.Owner
                                               GROUP BY u.Username
                                               HAVING COUNT(p.Owner) >= $From AND COUNT(p.Owner) <= $To))";
            }
            $result = mysqli_query($connection, $query);
        }
    }
}

$result = mysqli_query($connection, $query);
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result))
    {
        echo "<td>";
        $temp_user = $row['Username'];
        $count_query = "select count(Owner) as number from Property where Owner='$temp_user'";
        $count_result = mysqli_query($connection, $count_query);
        echo $temp_user;
        echo "</td>";
        echo "<td>";
        echo $row['Email'];
        echo "</td>";
        echo "<td>";
        $data=mysqli_fetch_assoc($count_result);
        echo $data['number'];
        echo "</td>";
        echo "<td><a href='view_owner.php?delete_owner=".$temp_user."'>Delete Owner</a></td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No owner stored";
}
echo "</table>";

// Search by
echo "<form action='view_owner.php' method='post'>
          <select id='adminSearchOwners' onchange='searchRange()' name='SearchBy'>
              <option value='' disabled selected>Search by</option>
              <option value='username'>Username</option>
              <option value='email'>Email</option>
              <option value='numProperty'>Number of Properties</option> 
          </select><br>
          <span id='term'><input type='text' placeholder='Search Term' name='SearchTerm'><br></span>
          <input value='Search Owners' type='submit'>
    </form>";

if(isset($_SESSION["adminName"])) {
    echo "<a href='admin_menu.php?name=".$_SESSION["adminName"]."'>Back</a><br/>";
}

mysqli_close($connection);
?>

<script>
function searchRange() {
    var x = document.getElementById('adminSearchOwners').value;
    if(x=='numProperty'){
        document.getElementById('term').innerHTML = "<input type='text' placeholder='From' name='From'> - <input type='text' placeholder='To' name='To'><br>";
    } else {
        document.getElementById('term').innerHTML = "<input type='text' placeholder='Search Term' name='SearchTerm'><br>";
    }
}
</script>

</body>
</html>