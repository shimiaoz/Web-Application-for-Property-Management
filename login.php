<?php
session_start();
require 'dbinfo.php';

$email = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to Connect");
    $email = $_POST['email'];
    $hashPswd = md5($_POST['password']);
    $query = "SELECT * FROM User WHERE Email='$email' AND Password='$hashPswd'";

    if($stmt=mysqli_prepare($connection, $query)) {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $result = mysqli_query($connection, $query);
        if(mysqli_stmt_num_rows($stmt) == 1) {
            while($row = mysqli_fetch_array($result))
            {
                if($row['UserType'] == "OWNER"){
                    header("Location: owner_function.php/?email=$email");
                }
                else if($row['UserType'] == "VISITOR"){
                    $visitor_username = $row['Username'];
                    header("Location: visitor_main_page.php?name=$visitor_username");
                }
                else if($row['UserType'] == "ADMIN"){
                    $admin_username = $row['Username'];
                    header("Location: admin_menu.php/?name=$admin_username");
                }
            }
        }
        else {
            echo "Login Failed";
        }   
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    Email: <input type="text" name="email" value="<?php echo $email; ?>" required><br/><br/>
    Password: <input type="password" name="password" required><br/><br/>
    <input value="Login" type="submit"><br/><br/>
</form>

<a href='register_owner.php'>New Owner Registration</a><br/>
<a href='register.php'>New Visitor Registration</a><br/>
</body>
</html>