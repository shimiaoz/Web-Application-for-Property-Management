<?php
session_start();
$username = $_GET['name'];
$_SESSION['adminName'] = $username;
?>
<html>
<title>Admin Menu</title>
<body>
<?php
echo "<h1>Welcome ".$username. "</h1>";
?>
<a href='http://localhost/phase3/view_visitors.php'>View Visitors</a><br>
<a href='http://localhost/phase3/view_owner.php'>View Owners</a><br/>
<a href='http://localhost/phase3/confirmed_properties.php'>View Confirmed Properties</a><br/>
<a href='http://localhost/phase3/unconfirmed_properties.php'>View Unconfirmed Properties</a><br/>
<a href='http://localhost/phase3/view_approved_animals_crops.php'>View Approved Animals and Crops</a><br/>
<a href='http://localhost/phase3/view_pending_animals_crops.php'>View Pending Animals and Crops</a><br/>
<a href='http://localhost/phase3/login.php'>Log out</a>

<body>
</html>