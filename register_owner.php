<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    .main {
        height: 450px;
    }

    .label-col {
        margin-top: 6px;
    }

    .input-col {
        margin-top: 6px;
    }

    .button-bottom {
        margin-top: 25px;
    }
    </style>
</head>
<body>
<?php
require 'dbinfo.php';

$username = $email = $password = "";
$property_name = $street = $city = $zip = $acres = "";
$property_type = $isPublic = $isCommercial = "";
$errMessage = "";

if (isset($_SESSION["connection_failure_message"]))
{
    $errMessage = $_SESSION["connection_failure_message"];
    unset($_SESSION["connection_failure_message"]);
}

$connection = new mysqli($host, $usernameDB, $passwordDB, $database);
if ($connection->connect_errno)
{
    $_SESSION["connection_failure_message"] = "System unavailable. Please try again later.";
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashPswd = md5($password);

    $property_name = $_POST['property_name'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $acres = (float) $_POST['acres'];
    $property_type = $_POST['property_type'];
    if (isset($_POST['animal']))
        $animal = $_POST['animal'];
    $crop = $_POST['crop'];
    $isPublic = $_POST['isPublic'];
    $isCommercial = $_POST['isCommercial'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        $errMessage = "invalid email address";
    elseif (strcmp($password, $_POST["confirm"]) != 0) 
        $errMessage = "passwords do not match";
    else
    {
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
        // Register the new owner account and new property
        else
        {
            // New property information
            $query_id = "SELECT * FROM Property";
            $result = $connection->query($query_id);
            $id = $result->num_rows + 1;
            
            // For new owner account registration
            $query_owner = "INSERT INTO User(Username, Email, Password, UserType) VALUES(?, ?, ?, 'OWNER')";
            $stmt_owner = $connection->prepare($query_owner);
            $stmt_owner->bind_param("sss", $username, $email, $hashPswd);
            $stmt_owner->execute();
            $stmt_owner->free_result();
            $stmt_owner->close();

            // For new property registration
            $query_property = "INSERT INTO Property VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
            $stmt_property = $connection->prepare($query_property);
            $stmt_property->bind_param("isdssssiss", $id, $property_name, $acres, $isCommercial, $isPublic, $street, $city, $zip, $property_type, $username);
            $stmt_property->execute();
            $stmt_property->free_result();
            $stmt_property->close();

            // Add items that the new property has
            $query_item = "INSERT INTO Has VALUES('$id', '$crop')";
            $connection->query($query_item);
            
            if (isset($_POST['animal']))
            {
                $query_item = "INSERT INTO Has VALUES('$id', '$animal')";
                $connection->query($query_item);
            }

            header("Location: login.php");
        }
    }
}
?>

<div class="main">
    <div class="topBar"></div>
    <div class="title">New Owner Registration</div>
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
                <input type="text" name="username" value="<?php echo $username; ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col">Email:</label></div>
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

        <div class="row">
            <div class="label-col"><label>Property Name:</label></div>
            <div class="input-col">
                <input type="text" name="property_name" value="<?php echo $property_name; ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col"><label>Street Address:</label></div>
            <div class="input-col">
                <input type="text" name="street" value="<?php echo $street; ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col"><label>City:</label></div>
            <div class="input-col short">
                <input type="text" name="city" value="<?php echo $city; ?>" required>
            </div>
            <div class="label-col short"><label>Zip:</label></div>
            <div class="input-col short">
                <input type="number" name="zip" value="<?php echo $zip; ?>" required>
            </div>
            <div class="label-col short"><label>Acres:</label></div>
            <div class="input-col short">
                <input type="number" name="acres" step="any" value="<?php echo $acres; ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col"><label>Property Type:</label></div>
            <div class="input-col short">
                <select id="propertySelect" name="property_type" onchange="change()" required>
                    <option value="" disabled <?php if (empty($property_type)) {echo "selected";} ?>>Select</option>
                    <?php
                        $property_array = array("Farm", "Garden", "Orchard");
                        foreach ($property_array as $property_tmp)
                        {
                            if (strcmp($property_type, $property_tmp) == 0)
                                $selected = "selected";
                            else
                                $selected = "";
                            $format = "<option value=%s %s>%s</option><br/>";
                            echo sprintf($format, strtoupper($property_tmp), $selected, $property_tmp);
                        }
                    ?>
                </select>
            </div>
            <div class="label-col short"><label>Animal:</label></div>
            <div class="input-col short">
                <select id='animalSelect' name="animal" required>
                    <option value="" disabled selected>Select</option>
                    <?php
                        $animal_list = $connection->query("SELECT * FROM FarmItem WHERE IsApproved = 1 AND Type = 'ANIMAL'");
                        if ($animal_list->num_rows > 0)
                        {
                            while ($row = $animal_list->fetch_assoc())
                            {
                                $tmp = $row['Name'];
                                echo "<option value='$tmp'>" . $tmp . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="label-col short"><label>Crop:</label></div>
            <div class="input-col short">
                <select name="crop" required>
                    <option value="" disabled selected>Select</option>
                    <?php
                        $corp_list = $connection->query("SELECT * FROM FarmItem WHERE IsApproved = 1 AND (Type = 'FRUIT' OR Type = 'FLOWER' OR Type = 'VEGETABLE' OR Type = 'NUT')");
                        if ($corp_list->num_rows > 0)
                        {
                            while($row = $corp_list->fetch_assoc())
                            {
                                $tmp = $row['Name'];
                                echo "<option value='$tmp'>" . $tmp . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="label-col">Public?:</label></div>
            <div class="input-col-radio">
                <input type="radio" name="isPublic" value="1" <?php if (strcmp($isPublic, "1") == 0) {echo "checked";} ?>>Yes
                &nbsp;
                <input type="radio" name="isPublic" value="0" <?php if (strcmp($isPublic, "1") != 0) {echo "checked";} ?>>No
            </div>
            <div class="label-col">Commercial?:</label></div>
            <div class="input-col-radio">
                <input type="radio" name="isCommercial" value="1" <?php if (strcmp($isCommercial, "1") == 0) {echo "checked";} ?>>Yes
                &nbsp;
                <input type="radio" name="isCommercial" value="0" <?php if (strcmp($isCommercial, "1") != 0) {echo "checked";} ?>>No
            </div>
        </div>

        <div class="button-bottom">
            <input class="button" type="submit" value="Register Owner">
            <input class="button" type="button" onclick="location.href='login.php';" value="Cancel">
        </div>
    </form>
</div>

<?php
$connection->close();
?>

<script>
function change() {
    var x = document.getElementById('propertySelect').value;
    if (x == 'FARM')
        document.getElementById('animalSelect').disabled = false;
    else
        document.getElementById('animalSelect').disabled = true;
}
</script>

</body>
</html>
