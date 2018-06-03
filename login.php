<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    .label-col {
        font-size: 1.15em;
    }

    .input-col input {
        font-size: 1.05em;
    }
    </style>
</head>
<body>

<?php
require "dbinfo.php";

$email = $errMessage = "";

if (isset($_SESSION["connection_failure_message"]))
{
    $errMessage = $_SESSION["connection_failure_message"];
    unset($_SESSION["connection_failure_message"]);
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Connect to database
    $connection = new mysqli($host, $usernameDB, $passwordDB, $database);
    if ($connection->connect_errno)
    {
        //Display error message at the same page when the connection fails
        $_SESSION["connection_failure_message"] = "System unavailable. Please try again later.";
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    }

    $email = $_POST["email"];
    $hashPswd = md5($_POST["password"]);
    $query = "SELECT * FROM User WHERE Email=? AND Password=?";

    // Retrieve the record
    if ($stmt = $connection->prepare($query))
    {
        $stmt->bind_param("ss", $email, $hashPswd);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1)
        {
            $row = $result->fetch_assoc();
            $username = $row["Username"];
            // Check UserType and redirect to the corresponding main page
            if ($row["UserType"] == "OWNER")
            {
                $_SESSION["User"] = array("UserType"=>"OWNER", "Username"=>$username);
                header("Location: owner_main_page.php?name=$username");
            }
            elseif($row["UserType"] == "VISITOR")
            {
                $_SESSION["User"] = array("UserType"=>"VISITOR", "Username"=>$username);
                header("Location: visitor_main_page.php?name=$username");
            }
            elseif($row["UserType"] == "ADMIN")
            {
                $_SESSION["User"] = array("UserType"=>"ADMIN", "Username"=>$username);
                header("Location: admin_menu.php?name=$username");
            }
        }
        else
            $errMessage = "Login is not successful. Please try signing in again.";

        $stmt->free_result();
        $stmt->close();
    }
    $connection->close();
}
?>

<div class="main">
    <div class="topBar"></div>
    <div class="title">Welcome to Property Management System</div>
    <?php
        if (!empty($errMessage))
        {
            $errMessage = "<i class='fa fa-exclamation-circle'></i> " . $errMessage;
            echo "<span class='errMessage'>" . $errMessage . "</span>";
        }
    ?>
    <br/>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="row">
            <div class="label-col"><label>Email:</label></div>
            <div class="input-col">
                <input type="text" name="email" value="<?php echo $email; ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="label-col"><label>Password:</label></div>
            <div class="input-col">
                <input type="password" name="password" required>
            </div>
        </div>
        <br/>
        <input class="button" type="submit" value="Login">
    </form>

    <div class="button-bottom">
        <!--New Owner Registration-->
        <input class="button" type="button" onclick="location.href='register_owner.php';" value="New Owner Registration">
        <!--New Visitor Registration-->
        <input class="button" type="button" onclick="location.href='register_visitor.php';" value="New Visitor Registration">
    </div>
</div>

</body>
</html>