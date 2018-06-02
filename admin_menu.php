<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Administrator Menu</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .main {
            height: 450px;
        }
        .button {
            margin-top: 12px;
            width: 55%;
        }
    </style>
</head>
<body>
<?php
$username = $_SESSION["USER"]["Username"];
echo <<<EOT
<div class="main">
    <div class="topBar"></div>
    <div class="title">Welcome $username</div>
    <div><input class="button" type="button" onclick="location.href='view_visitors.php';" value="View Visitors"></div>
    <div><input class="button" type="button" onclick="location.href='view_owners.php';" value="View Owners"></div>
    <div><input class="button" type="button" onclick="location.href='confirmed_properties.php';" value="View Confirmed Properties"></div>
    <div><input class="button" type="button" onclick="location.href='unconfirmed_properties.php';" value="View Unconfirmed Properties"></div>
    <div><input class="button" type="button" onclick="location.href='view_approved_animals_crops.php';" value="View Approved Animals and Crops"></div>
    <div><input class="button" type="button" onclick="location.href='view_pending_animals_crops.php';" value="View Pending Animals and Crops"></div>
    <div><input class="button" type="button" onclick="location.href='login.php';" value="Log out"></div>
</div>
EOT;
?>
<body>
</html>
