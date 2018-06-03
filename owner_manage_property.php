<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Property</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    .main {
        height: 450px;
    }

    .label-col,
    .input-col {
        margin-top: 6px;
    }

    .input-col input,
    .input-col select {
        width: 90%;
        font-size: 0.9em;
    }

    .col-33,
    .request {
        margin-top: 24px;
    }

    .col-33 .label-col {
        width: 35%;
    }

    .col-33 .input-col {
        width: 65%;
    }

    .col-33 input[type=submit],
    .request input[type=submit] {
        margin-top: 6px;
        cursor: pointer;
        background-color: #CCCCCC;
        padding-top: 1px;
        padding-bottom: 1px;  
    }

    .request input,
    .request select {
        width: 18%;
        font-size: 0.9em;
    }

    .request input[type=submit] {
        margin-top: 0px;
    }

    #delete_crop,
    #delete_animal {
        width: 70%;
    }

    #delete_crop_button,
    #delete_animal_button {
        margin-top: 0px;
        width: 18%;
        border-left: 0px;
    }

    #delete_property_form {
        display: inline-block;
    }
    </style>
    <script src="helperFunctions.js"></script>
</head>
<body>
<?php
require 'dbinfo.php';
$connection = new mysqli($host, $usernameDB, $passwordDB, $database);

$username = $_SESSION["User"]["Username"];
$propertyID = $_GET['ID'];
$query = "SELECT * FROM Property WHERE ID = '$propertyID'";

if (isset($_POST['update_property']))
{
    $property_name = $_POST['property_name'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $acres = (float) $_POST['acres'];
    $isPublic = $_POST['isPublic'];
    $isCommercial = $_POST['isCommercial'];

    // Update property
    $query_property = "UPDATE Property SET Name=?, Street=?, City=?, Zip=?, Size =?, IsPublic=?, IsCommercial=? WHERE ID=?";
    $stmt_property = $connection->prepare($query_property);
    $stmt_property->bind_param("sssidssi", $property_name, $street, $city, $zip, $acres, $isPublic, $isCommercial, $propertyID);
    $stmt_property->execute();
    $stmt_property->free_result();
    $stmt_property->close();
}
elseif (isset($_POST["add_crop"]) || isset($_POST["add_animal"]))
{
    if (isset($_POST["add_crop"]))
        $item = $_POST["add_crop"];
    else
        $item = $_POST["add_animal"];

    $query_item = "INSERT INTO Has VALUES('$propertyID', '$item')";
    $connection->query($query_item);
}
elseif (isset($_POST["delete_crop"]) || isset($_POST["delete_animal"]))
{
    if (isset($_POST["delete_crop"]))
        $item = $_POST["delete_crop"];
    else
        $item = $_POST["delete_animal"];

    $query_item = "DELETE FROM Has WHERE PropertyID = '$propertyID' AND ItemName = '$item'";
    $connection->query($query_item);
}
elseif (isset($_POST["request_item"]))
{
    $item_name = $_POST["new_item_name"];
    $item_type = $_POST["new_item_type"];
    $query_request = "INSERT INTO FarmItem VALUES('$item_name', 0, '$item_type')";
    $connection->query($query_request);
}
elseif (isset($_POST["delete_property"])) {
    $query_delete = "DELETE FROM Property WHERE ID = '$propertyID'";
    $connection->query($query_delete);
    header("Location: owner_main_page.php?name=$username");
}

$result = $connection->query($query);
$row = $result->fetch_assoc();
?>

<div class="main">
    <div class="topBar"></div>
    <div class="title"><?php echo "Manage " . $row["Name"]; ?></div>
    <form id="update_property" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
        <div class="row">
            <div class="col-left">
                <div class="label-col"><label>Name:</label></div>
                <div class="input-col">
                    <input type="text" name="property_name" value="<?php echo $row['Name']; ?>" required>
                </div>
            </div>
            <div class="col-right">
                <div class="label-col"><label>ID:</label></div>
                <div class="input-col"><?php echo str_pad($row["ID"], 5, '0', STR_PAD_LEFT); ?></div>
            </div>
        </div>

        <div class="row">
            <div class="col-left">
                <div class="label-col"><label>Street:</label></div>
                <div class="input-col">
                    <input type="text" name="street" value="<?php echo $row['Street']; ?>" required>
                </div>
            </div>
            <div class="col-right">
                <div class="label-col"><label>Type:</label></div>
                <div id="ptype" class="input-col"><?php echo ucwords(strtolower($row['PropertyType'])); ?></div>
            </div>
        </div>

        <div class="row">
            <div class="col-left">
                <div class="label-col"><label>City:</label></div>
                <div class="input-col">
                    <input type="text" name="city" value="<?php echo $row['City']; ?>" required>
                </div>
            </div>
            <div class="col-right">
                <div class="label-col"><label>Public:</label></div>
                <div class="input-col">
                    <select name="isPublic" required>
                        <option value="1" <?php if (strcmp($row['IsPublic'], "1") == 0) echo "selected"; ?>>True</option>
                        <option value="0" <?php if (strcmp($row['IsPublic'], "0") == 0) echo "selected"; ?>>False</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-left">
                <div class="label-col"><label>Zip:</label></div>
                <div class="input-col">
                    <input type="number" name="zip" value="<?php echo $row['Zip']; ?>" required>
                </div>
            </div>
            <div class="col-right">
                <div class="label-col"><label>Commercial:</label></div>
                <div class="input-col">
                    <select name="isCommercial" required>
                        <option value="1" <?php if (strcmp($row['IsCommercial'], "1") == 0) echo "selected"; ?>>True</option>
                        <option value="0" <?php if (strcmp($row['IsCommercial'], "0") == 0) echo "selected"; ?>>False</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-left">
                <div class="label-col"><label>Size (acres):</label></div>
                <div class="input-col">
                    <input type="number" name="acres" step="any" value="<?php echo $row['Size']; ?>" required>
                </div>
            </div>
        </div>
    </form>

    <div id="item_row" class="row">
        <div class="col-33">
            <div class="label-col"><label>Add New Crop:</label></div>
            <div class="input-col">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
                    <select name="add_crop">
                        <option value="" disabled selected>Select</option>
                        <?php
                            $query_crop = "SELECT Name FROM FarmItem WHERE IsApproved=1 AND TYPE!='ANIMAL' AND (Name NOT IN (SELECT ItemName FROM Has WHERE PropertyID='$propertyID'))";
                            $approved_crops = $connection->query($query_crop);
                            if ($approved_crops->num_rows > 0)
                            {
                                while ($row = $approved_crops->fetch_assoc())
                                {
                                    $tmp = $row['Name'];
                                    echo "<option value='$tmp'>" . $tmp . "</option>";
                                }
                            }
                        ?>
                    </select>
                    <div><input type="submit" value="Add Crop"></div>
                </form>
            </div>
        </div>
        
        <div class="col-33 forFarm">
            <div class="label-col"><label>Add New Animal:</label></div>
            <div class="input-col">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
                    <select name="add_animal" required>
                        <option value="" disabled selected>Select</option>
                        <?php
                            $query_animal = "SELECT Name FROM FarmItem WHERE IsApproved=1 AND Type='ANIMAL' AND (Name NOT IN (SELECT ItemName FROM Has WHERE PropertyID='$propertyID'))";
                            $approved_animals = $connection->query($query_animal);
                            if ($approved_animals->num_rows > 0)
                            {
                                while ($row = $approved_animals->fetch_assoc())
                                {
                                    $tmp = $row['Name'];
                                    echo "<option value='$tmp'>" . $tmp . "</option>";
                                }
                            }
                        ?>
                    </select>
                    <div><input type="submit" value="Add Animal"></div>
                </form>
            </div>
        </div>
        
        <div class="col-33">
            <div class="label-col"><label>Crops:</label></div>
            <div class="input-col">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
                    <select id="delete_crop" name="delete_crop" required>
                        <option value="" disabled selected>Select</option>
                        <?php
                            $query_crop = "SELECT Has.ItemName FROM Has INNER JOIN FarmItem on Has.ItemName=FarmItem.Name WHERE TYPE!='ANIMAL' AND PropertyID='$propertyID'";
                            $property_crops = $connection->query($query_crop);
                            if ($property_crops->num_rows > 0)
                            {
                                while ($row = $property_crops->fetch_assoc())
                                {
                                    $tmp = $row['ItemName'];
                                    echo "<option value='$tmp'>" . $tmp . "</option>";
                                }
                            }
                        ?>
                    </select><input id="delete_crop_button" type="submit" value="×">
                </form>
            </div>
            <div class="label-col forFarm"><label>Animals:</label></div>
            <div class="input-col forFarm">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
                    <select id="delete_animal" name="delete_animal" required>
                        <option value="" disabled selected>Select</option>
                        <?php
                            $query_animal = "SELECT Has.ItemName FROM Has INNER JOIN FarmItem on Has.ItemName=FarmItem.Name WHERE TYPE='ANIMAL' AND PropertyID='$propertyID'";
                            $property_animals = $connection->query($query_animal);
                            if ($property_animals->num_rows > 0)
                            {
                                while ($row = $property_animals->fetch_assoc())
                                {
                                    $tmp = $row['ItemName'];
                                    echo "<option value='$tmp'>" . $tmp . "</option>";
                                }
                            }
                        ?>
                    </select><input id="delete_animal_button" type="submit" value="×">
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="request">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
                <label>Request <span class="forFarm">Animal/</span>Crop Approval:</label>
                <select name="new_item_type">
                    <option value="" disabled selected>Select Type</option>
                    <option class="forFarm" value="ANIMAL">Animal</option>
                    <option value="FRUIT">Fruit</option>
                    <option value="FLOWER">Flower</option>
                    <option value="VEGETABLE">Vegetable</option>
                    <option value="NUT">Nut</option>
                </select>
                <input type="text" name="new_item_name" placeholder="Enter name">
                <input type="submit" name="request_item" value="Submit Request">
            </form>
        </div>
    </div>

    <div class="button-bottom">
        <form id="delete_property_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?ID=$propertyID"; ?>" method="post">
            <input class="button" type="submit" name="delete_property" value="Delete Property">
        </form>
        <input class="button" type="submit" form="update_property" name="update_property" value="Save Changes">
        <input class="button" type="button" onclick="location.href='owner_main_page.php<?php echo "?name=$username"; ?>';" value="Back (Don't Save)">
    </div>
</div>

<?php
$connection->close();
?>
<script>changeDisplay()</script>

</body>
</html>