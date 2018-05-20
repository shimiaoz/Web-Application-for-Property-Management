<?php
session_start();
require 'dbinfo.php';

$email = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $connection = new mysqli($host, $usernameDB, $passwordDB, $database);
    if ($connection->connect_errno) {
        //printf("Unable to Connect: %s\n", mysqli_connect_error());
        echo "Unable to Connect";
        exit();
    }

    $email = $_POST['email'];
    $hashPswd = md5($_POST['password']);
    $query = "SELECT * FROM User WHERE Email=? AND Password=?";

    if($stmt = $connection->prepare($query)) {
        $stmt->bind_param("ss", $email, $hashPswd);
        $stmt->execute();
        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        if($num_of_rows == 1) {
            $row = $result->fetch_assoc();
            if($row['UserType'] == "OWNER"){
                header("Location: owner_function.php?email=$email");
            }
            else if($row['UserType'] == "VISITOR"){
                $visitor_username = $row['Username'];
                header("Location: visitor_main_page.php?name=$visitor_username");
            }
            else if($row['UserType'] == "ADMIN"){
                $admin_username = $row['Username'];
                header("Location: admin_menu.php?name=$admin_username");
            }
        }
        else {
            echo "Login Failed";
        }

        $stmt->free_result();
        $stmt->close();
    }
    $connection->close();
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
