<?php
// Start the session
session_start();
require 'dbinfo.php' ; 
?>  
<html>
<title>Visitor Register page</title>
<body>

<?php
if(isset($_POST['subbutton'])){
    $pslength=strlen($_POST['password']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['name'];

if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
    echo "Invalid email address";
} else {
    if(strcmp($password,$_POST['confirm']) != 0) {
        echo "password not match!";
    }

    if($pslength < 8){
        echo "password must have at least 8 characters!";
    } else {
        $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");      
        $hashPswd = md5($password);
        $query = "INSERT INTO User(Username, Email, Password, UserType) VALUES('$username', '$email', '$hashPswd', 'VISITOR')";
        if(mysqli_query($connection, $query)) {
            echo "User '$username' registered successfully";
            header("Location: login.php");
        } else {
            echo "Registration failed";
        }
    }
}
}
?>

<form action="register.php" method="post">
Username: <input type="text" name="name"><br>
E-mail: <input type="text" name="email"><br>
Password: <input type="password" name="password"><br>
Confirm password: <input type="password" name="confirm"><br>
<input name="subbutton" type="submit"><br>
</form>

<a href='login.php'>Cancel</a><br/>
</body>
</html>