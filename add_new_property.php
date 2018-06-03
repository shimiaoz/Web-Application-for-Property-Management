<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Property</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    .main {
        height: 400px;
    }

    .label-col {
        width: 25%;
        font-size: 1.1em;
    }

    .label-col.short {
        width: 13%;
    }

    .input-col-radio {
        margin-top: 13px;
        width: 50%;
    }

    .button-bottom {
        margin-top: 25px;
    }
    </style>
    <script src="helperFunctions.js"></script>
</head>
<body>
<?php
require 'dbinfo.php';
$connection = new mysqli($host, $usernameDB, $passwordDB, $database);

$username = $_SESSION["User"]["Username"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
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

    // New property information
    $query_id = "SELECT * FROM Property";
    $result = $connection->query($query_id);
    $id = $result->num_rows + 1;

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
        if (strcmp($property_type, "FARM") == 0)
        {
            $query_item = "INSERT INTO Has VALUES('$id', '$animal')";
            $connection->query($query_item);
        }
    }

    $message = "<i class='fa fa-info-circle'></i> The new property is successfully added";
}
?>

<div class="main">
    <div class="topBar"></div>
    <div class="title">Add New Property</div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="row">
            <div class="label-col"><label>Property Name:</label></div>
            <div class="input-col">
                <input type="text" name="property_name" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col"><label>Street Address:</label></div>
            <div class="input-col">
                <input type="text" name="street" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col"><label>City:</label></div>
            <div class="input-col short">
                <input type="text" name="city" required>
            </div>
            <div class="label-col short"><label>Zip:</label></div>
            <div class="input-col short">
                <input type="number" name="zip" required>
            </div>
            <div class="label-col short"><label>Acres:</label></div>
            <div class="input-col short">
                <input type="number" name="acres" step="any" required>
            </div>
        </div>

        <div class="row">
            <div class="label-col"><label>Property Type:</label></div>
            <div class="input-col short">
                <select id="propertySelect" name="property_type" onchange="animalOption()" required>
                    <option value="" disabled selected>Select</option>
                    <option value="FARM">Farm</option>
                    <option value="GARDEN">Garden</option>
                    <option value="ORCHARD">Orchard</option>
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
                                $tmp = $row["Name"];
                                echo "<option value='$tmp'>" . $tmp . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div id='animalSelect'>
                <div class="label-col short"><label>Animal:</label></div>
                <div class="input-col short">
                    <select name="animal" required>
                        <option value="" disabled selected>Select</option>
                        <?php
                            $animal_list = $connection->query("SELECT * FROM FarmItem WHERE IsApproved = 1 AND Type = 'ANIMAL'");
                            if ($animal_list->num_rows > 0)
                            {
                                while ($row = $animal_list->fetch_assoc())
                                {
                                    $tmp = $row["Name"];
                                    echo "<option value='$tmp'>" . $tmp . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="label-col">Public?:</label></div>
            <div class="input-col-radio">
                <input type="radio" name="isPublic" value="1">Yes
                &nbsp;
                <input type="radio" name="isPublic" value="0" checked="">No
            </div>
        </div>

        <div class="row">
            <div class="label-col">Commercial?:</label></div>
            <div class="input-col-radio">
                <input type="radio" name="isCommercial" value="1">Yes
                &nbsp;
                <input type="radio" name="isCommercial" value="0" checked="">No
            </div>
        </div>

        <div class="button-bottom">
            <input class="button" type="submit" value="Add Property">
            <input class="button" type="button" onclick="location.href='owner_main_page.php?name=$username';" value="Cancel">
        </div>
    </form>
    <?php
        if (!empty($message))
            echo "<div class='message'>" . $message . "</div>";
    ?>
</div>

<?php
$connection->close();
?>

</body>
</html>