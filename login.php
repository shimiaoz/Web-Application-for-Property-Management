<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-weight: bold;
    }
    .main {
        position: absolute;
        border: 1px solid #000000;
        background-color: #F3F3F3;
        width: 600px;
        height: 350px;
        top: 50%;
        left: 50%;
        transform: translateX(-50%) translateY(-50%);
    }
    .topBar {
        position: relative;
        border-style: solid;
        border-width: 0px 0px 1px 0px;
        border-color: #000000;
        background-color: #CCCCCC;
        width: 100%;
        height: 10%;
    }
    .title {
        text-align: center;
        padding-top: 10px;
        padding-bottom: 25px;
        font-size: 1.8em;
    }
    #loginPage {
        text-align: center;
        vertical-align: middle;
    }
    input[type=text],
    input[type=password] {
        width: 65%;
        margin: 0px 2px;
    }
    .input {
        width: 75%;
        padding: 5px 12px;
        text-align: right;
        font-size: 1.2em;
        transform: translateX(5%)
    }
    .errMessage {
        color: #FF0000;
    }
    .button {
        background-color: #CCCCCC;
        border: 1px solid #000000;
        border-radius: 4px;
        padding: 5px 8px;
        margin: 0px 12px;
        font-family: "Times New Roman", Times, serif;
        font-weight: bold;
        font-size: 1.2em;
        cursor: pointer;
    }
</style>
</head>
<body>

<?php
require 'dbinfo.php';

$email = $errMessage = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to database
    $connection = new mysqli($host, $usernameDB, $passwordDB, $database);
    if ($connection->connect_errno) {
        //printf("Unable to Connect: %s\n", mysqli_connect_error());
        echo "Unable to Connect";
        exit();
    }

    $email = $_POST["email"];
    $hashPswd = md5($_POST["password"]);
    $query = "SELECT * FROM User WHERE Email=? AND Password=?";

    // Retrieve the record
    if($stmt = $connection->prepare($query)) {
        $stmt->bind_param("ss", $email, $hashPswd);
        $stmt->execute();
        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        if($num_of_rows == 1) {
            $row = $result->fetch_assoc();

            // Check UserType and redirect to the corresponding main page
            if($row["UserType"] == "OWNER"){
                header("Location: owner_function.php?email=$email");
            }
            else if($row["UserType"] == "VISITOR"){
                $visitor_username = $row["Username"];
                header("Location: visitor_main_page.php?name=$visitor_username");
            }
            else if($row["UserType"] == "ADMIN"){
                $admin_username = $row["Username"];
                header("Location: admin_menu.php?name=$admin_username");
            }
        }
        else {
            $errMessage = "* Login is not successful. Please try signing in again.";
        }

        $stmt->free_result();
        $stmt->close();
    }
    $connection->close();
}
?>

<div class="main">
    <div class="topBar"></div>
    <div class="title">Welcome to Property Management System</div>
    <div id="loginPage">
        <?php echo "<span class=errMessage>" . $errMessage . "</span>"; ?><br/>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input">
                Email: <input type="text" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="input">
                Password: <input type="password" name="password" required>
            </div>
            <br/><br/>
            <button class="button" type="submit">Login</button>
        </form>
        <br/>
        <!--New Owner Registration-->
        <input class="button" type="button" onclick="location.href='register_owner.php';" value="New Owner Registration">
        <!--New Visitor Registration-->
        <input class="button" type="button" onclick="location.href='register.php';" value="New Visitor Registration">
    </div>
</div>

</body>
</html>
