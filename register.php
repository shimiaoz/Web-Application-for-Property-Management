<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html>
<title>Visitor Registration</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="style.css">
<style>
</style>
<body>

<?php
require "dbinfo.php" ; 

$username = $email = $password = $errMessage = "";

if (isset($_SESSION["connection_failure_message"]))
{
    $errMessage = $_SESSION["connection_failure_message"];
    unset($_SESSION["connection_failure_message"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    //$pslength = strlen($_POST['password']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        $errMessage = "invalid email address";
    elseif (strcmp($password, $_POST["confirm"]) != 0) 
        $errMessage = "passwords do not match";
    /*
    elseif ($pslength < 8)
        echo "Password must have at least 8 characters!";
    */
    else 
    {
        $connection = new mysqli($host, $usernameDB, $passwordDB, $database);
        if ($connection->connect_errno)
        {
            $_SESSION["connection_failure_message"] = "System unavailable. Please try again later.";
            header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        }

        // Check if the username or Email already exists in the system
        $user_exist_check_query = "SELECT Username, Email FROM User WHERE Username=? OR Email=?";
        $tmp_stmt = $connection->prepare($user_exist_check_query);
        $tmp_stmt->bind_param("ss", $username, $email);
        $tmp_stmt->execute();
        $tmp_result = $tmp_stmt->get_result();
        
        // Account already exists
        if ($tmp_result->num_rows != 0)
        {
            $tmp_row = $tmp_result->fetch_assoc();
            if (strcmp($tmp_row["Username"], $username) == 0)
                $errMessage = "this username already exists";
            elseif (strcmp($tmp_row["Email"], $email) == 0)
                $errMessage = "this email already exists";

            $tmp_stmt->free_result();
            $tmp_stmt->close();
        }
        // Register the new account
        else
        {
            $hashPswd = md5($password);
            $query = "INSERT INTO User(Username, Email, Password, UserType) VALUES(?, ?, ?, 'VISITOR')";
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sss", $username, $email, $hashPswd);
            $stmt->execute();
            
            header("Location: login.php");
            $stmt->free_result();
            $stmt->close();
        }
        $connection->close();
    }
}
?>

<div class="main">
    <div class="topBar"></div>
    <div class="title">New Visitor Registration</div>
    <?php
        if (!empty($errMessage))
        {
            $errMessage = "<i class='fa fa-exclamation-circle'></i> Registration failed: " . $errMessage;
            echo "<span class='errMessage'>" . $errMessage . "</span>";
        }
    ?>
    <br/>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="row">
            <div class="label-col"><label>Username:</label></div>
            <div class="input-col">
                <input type="text" name="name" value="<?php echo $username; ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="label-col">E-mail:</label></div>
            <div class="input-col">
                <input type="text" name="email" value="<?php echo $email; ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="label-col">Password:</label></div>
            <div class="input-col">
                <input type="password" name="password" pattern=".{8,}" value="<?php echo $password; ?>" required>
                <div class="tooltip">
                    <i class='fa fa-info-circle'></i>
                    <span class="tooltiptext">Require at least 8 characters</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="label-col">Confirm password:</label></div>
            <div class="input-col">
                <input type="password" name="confirm" required>
            </div>
        </div>
        <div class="button-bottom">
            <input class="button" type="submit" value="Register Visitor">
            <input class="button" type="button" onclick="location.href='login.php';" value="Cancel">
        </div>
    </form>
</div>

</body>
</html>
