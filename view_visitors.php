<html>
<body>
<?php
session_start();
require 'dbinfo.php';

echo "<h1>All Visitors In System</h1>";

$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database);
if(isset($_GET['delete_visitor'])) {
    $temp_username = $_GET['delete_visitor'];
    $delete_query = "Delete from User where Username='$temp_username'";
    mysqli_query($connection, $delete_query);
}

if(isset($_GET['delete_log'])) {
    $temp_username = $_GET['delete_log'];
    $delete_query = "Delete from Visit where Username='$temp_username'";
    mysqli_query($connection, $delete_query);
}

$query = "SELECT * FROM User WHERE UserType='VISITOR'";


echo "<table>";
echo "<tr>";
echo "<th>Username <a href='view_visitors.php?sort_name=0'>↑</a> <a href='view_visitors.php?sort_name=1'>↓</a></th>";
echo "<th>Email<a href='view_visitors.php?sort_email=0'>↑</a> <a href='view_visitors.php?sort_email=1'>↓</a></th>";
echo "<th>Visits<a href='view_visitors.php?sort_loggedvisits=0'>↑</a> <a href='view_visitors.php?sort_loggedvisits=1'>↓</a></th>";
echo "</tr>";
echo "<tr>";

if(isset($_GET['sort_name'])) {
    $temp_order = $_GET['sort_name'];
    if($temp_order==0) {
        $query = $query. " order by username asc";
    } else {
        $query = $query. " order by username desc";
    }
}

if(isset($_GET['sort_email'])) {
    $temp_order = $_GET['sort_email'];
    if($temp_order==0) {
        $query = $query. " order by email asc";
    } else {
        $query = $query. " order by email desc";
    }
}

if(isset($_GET['sort_loggedvisits'])) {
    $temp_order = $_GET['sort_loggedvisits'];
    if($temp_order==0) {
        $query = "SELECT u.* FROM User AS u
                  LEFT JOIN Visit AS v ON u.Username = v.Username
                  WHERE u.UserType='VISITOR'
                  GROUP BY u.Username
                  ORDER BY COUNT(v.Username) ASC";
    } else {
        $query = "SELECT u.* FROM User AS u
                  LEFT JOIN Visit AS v ON u.Username = v.Username
                  WHERE u.UserType='VISITOR'
                  GROUP BY u.Username
                  ORDER BY COUNT(v.Username) DESC";
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
            if($attr=='visit'){
                $query .= " AND (Username IN (SELECT u.Username FROM User as u
                                               LEFT JOIN Visit AS v ON u.Username = v.Username
                                               GROUP BY u.Username
                                               HAVING COUNT(v.Username) >= $From AND COUNT(v.Username) <= $To))";
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
        $count_query = "select count(Username) as visit_number from Visit where Username='$temp_user'";
        $count_result = mysqli_query($connection, $count_query);
        echo $temp_user;
        echo "</td>";
        echo "<td>";
        echo $row['Email'];
        echo "</td>";
        echo "<td>";
        $data=mysqli_fetch_assoc($count_result);
        echo $data['visit_number'];
        echo "</td>";
        echo "<td><a href='view_visitors.php?delete_visitor=".$temp_user."'>Delete Visitor</a></td>";
        echo "<td><a href='view_visitors.php?delete_log=".$temp_user."'>Delete Log</a></td>";
        echo "</tr>";
        echo "<tr>";
    }
    echo "</tr>";
}
else {
    echo "No visitor stored";
}
echo "</table>";

// Search by
echo "<form action='view_visitors.php' method='post'>
          <select id='adminSearchVisitor' onchange='searchRange()' name='SearchBy'>
              <option value='' disabled selected>Search by</option>
              <option value='username'>Username</option>
              <option value='email'>Email</option>
              <option value='visit'>Visits</option> 
          </select><br>
          <span id='term'><input type='text' placeholder='Search Term' name='SearchTerm'><br></span>
          <input value='Search Visitors' type='submit'>
    </form>";

if(isset($_SESSION["adminName"])) {
    echo "<a href='admin_menu.php?name=".$_SESSION["adminName"]."'>Back</a><br/>";
}

mysqli_close($connection);
?>

<script>
function searchRange() {
    var x = document.getElementById('adminSearchVisitor').value;
    if(x=='visit'){
        document.getElementById('term').innerHTML = "<input type='text' placeholder='From' name='From'> - <input type='text' placeholder='To' name='To'><br>";
    } else {
        document.getElementById('term').innerHTML = "<input type='text' placeholder='Search Term' name='SearchTerm'><br>";
    }
}
</script>

</body>
</html>
