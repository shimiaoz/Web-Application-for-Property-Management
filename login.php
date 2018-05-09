<?php
// Start the session
session_start();

require 'dbinfo.php';

$email = $password = "";
$emailErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    }
    else {
        $email = $_POST["email"];
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    }
    else {
        $password = $_POST["password"];
    }
}

if(!empty($_POST['email']) && !empty($_POST['password'])) {
    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
    $hashPswd = md5($password);
    $query = "SELECT * FROM User WHERE Email='$email' AND Password='$hashPswd'";

    if($stmt=mysqli_prepare($connection, $query)) {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $result = mysqli_query($connection, $query);
        if(mysqli_stmt_num_rows($stmt) == 1) {
            echo "User '$email' login successfully. ";

            while($row = mysqli_fetch_array($result))
            {
                if($row['UserType'] == "OWNER"){
                    header("Location: owner_function.php/?email=$email");
                }else if($row['UserType'] == "VISITOR"){
                    $visitor_username = $row['Username'];
                    header("Location: visitor_main_page.php?name=$visitor_username");
                }else if($row['UserType'] == "ADMIN"){
                    $admin_username = $row['Username'];
                    header("Location: admin_menu.php/?name=$admin_username");
                }
            }
        }
        else {
            echo "Login failed!";
        }   
    }
    else {
        echo "Connection Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
</head>
<body>

<form action="login.php" method="post">
    Email: <input type="text" name="email"><span>* <?php echo $emailErr;?></span><br>
    Password: <input type="password" name="password"><span>* <?php echo $passwordErr;?></span><br>
    <input value="Login" type="submit">
</form>

<a href='register_owner.php'>New Owner Registration</a><br/>
<a href='register.php'>New Visitor Registration</a><br/>
</body>
</html>
