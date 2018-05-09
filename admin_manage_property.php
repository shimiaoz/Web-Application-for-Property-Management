<?php
// Start the session
session_start();
require 'dbinfo.php' ; 
?>
<html>
<title>Admin Manage Property</title>
<body>


<?php
$id = $_GET['ID'];
echo "Got myID: '$id'";
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
        
$tmp = mysqli_query($connection, "SELECT * FROM Property WHERE ID = '$id'");
while($row = mysqli_fetch_array($tmp)){
    $property_name = $row['Name'];
    echo " this is pname: '$property_name' ";
    $street_adr = $row['Street'];
    $city = $row['City'];
    $zip = $row['Zip'];
    $acres = $row['Size'];
    $property_type = $row['PropertyType'];
    //echo " property_type: $property_type. ";
    $isPublic = $row['IsPublic'];
    $isCommercial = $row['IsCommercial'];
    $owner = $row['Owner'];
    
    if($isPublic){
        $curP = "Yes";
    }else{
        $curP = "No";
    }
    if($isCommercial){
        $curC = "Yes";
    }else{
        $curC = "No";
    }
    mysqli_close($connection);
}
if(isset($_POST['submit'])) { 
        //$property_name = $_POST['property_name'];
        $street_adr = $_POST['street_adr'];
        $city = $_POST['city'];
        $zip = intval($_POST['zip']);
        $acres = (float)$_POST['acres'];
        if(isset($_POST['propertyType'])){
            $property_type = $_POST['propertyType'];
        }
        if(isset($_POST['animal'])){
            $animal = $_POST['animal'];
            $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
            $animal_update = "INSERT INTO Has VALUES('$id', '$animal')";
                if(mysqli_query($connection, $animal_update) ) {
                        echo "Has Animal updated successfully\n";
                } else {
                       
                        echo "Has Animal Update failed\n";
                }
           mysqli_close($connection);
        }
        if(isset($_POST['crop'])){
            $crop = $_POST['crop'];
            echo "crop: $crop ";
            $crop_update = "INSERT INTO Has VALUES('$id', '$crop')";
            $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
            if(mysqli_query($connection, $crop_update) ) {
                        echo "Has Crop updated successfully\n";
                } else {
                       
                        echo "Has Crop Update failed\n";
                }
                mysqli_close($connection);
        }
        if(isset($_POST['isPublic'])){
            $isPublic = $_POST['isPublic'];
        }
        if(isset($_POST['isCommercial'])){
            $isCommercial = $_POST['isCommercial'];
        }
        $owner = $_POST['owner'];
        
        $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
        
        $query = "UPDATE Property SET Size = '$acres', Street = '$street_adr', City = '$city', Zip = '$zip', PropertyType = '$property_type', Owner = '$owner', IsCommercial = '$isCommercial', IsPublic = '$isPublic' WHERE ID = '$id'";
        //$result
       
        if(mysqli_query($connection, $query)) {
            echo "'$id', '$property_name'  \n ";
            echo "Property '$property_name' updated successfully\n";
        } else {
            echo " '$id', '$property_name', '$owner', '$street_adr', '$zip', '$city', '$acres', '$property_type'  \n ";
            echo "Property Update failed\n";
        }
        
        
        mysqli_close($connection);
    
}
if(isset($_POST['delete'])){
    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
    
        $queryAddItem = "DELETE FROM Property WHERE ID = '$id'";
        if(mysqli_query($connection, $queryAddItem)){
            echo "delete succeed";
            $name_query = "SELECT Email FROM User WHERE Username = '$owner'";
            $tmp_result = mysqli_query($connection, $name_query);
            $tmp_row = mysqli_fetch_assoc($tmp_result);
            $email = $tmp_row['Email'];
            header("Location: owner_function.php/?email=$email");
        }else {
            echo "delete failed";
        }
    mysqli_close($connection);
}
if(isset($_POST['deleteCrop'])){
    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
    $curC = $_POST['curCrop'];
    echo "curC : $curC";
        $deleteCrop = "DELETE FROM Has WHERE PropertyID = '$id' AND ItemName = '$curC'";
        if(mysqli_query($connection, $deleteCrop)){
            echo "delete Crop succeed";
        }else {
            echo "delete Crop failed";
        }
    mysqli_close($connection);
}
if(isset($_POST['deleteAnimal'])){
    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
    $curA = $_POST['curAnimal'];
        $deleteAnimal = "DELETE FROM Has WHERE PropertyID = '$id' AND ItemName = '$curA'";
        if(mysqli_query($connection, $deleteAnimal)){
            echo "delete Animal succeed";
        }else {
            echo "delete Animal failed";
        }
    mysqli_close($connection);
}
?>

<form  action='admin_manage_property.php?ID=<?php echo $_GET['ID'];?>' method='post'>

<!-- Property Name: <input type="text" name="property_name" value="<?php echo $property_name; ?>"><br> -->
Owner: <input type="text" name="owner" value="<?php echo $owner; ?>"><br>
Street Address: <input type="text" name="street_adr" value="<?php echo $street_adr; ?>"><br>
City: <input type="text" name="city" value="<?php echo $city; ?>"><br>
Zip: <input type="text" name="zip" value="<?php echo $zip; ?>"><br>
Acres: <input type="text" name="acres" value="<?php echo $acres; ?>"><br>
<?php
$isID = $_GET['ID'];
$connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
$isFarm = mysqli_query($connection, "SELECT * FROM Property WHERE ID = '$isID' ");
while($row = mysqli_fetch_array($isFarm)){
                        $tmp = $row['PropertyType'];
                        }
echo "Property Type: " .$tmp. "<br>";
if($tmp == 'FARM'){
?>
Current Animal:     <select name="curAnimal">
                    <?php
                    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
                    $curAnimal_list=mysqli_query($connection, "SELECT * FROM FarmItem, Has WHERE Has.PropertyID = '$id' AND Has.ItemName = FarmItem.Name AND FarmItem.Type = 'ANIMAL'");
                    while($row = mysqli_fetch_array($curAnimal_list)){
                        $tmp = $row['Name'];
                        echo "<option value='$tmp'>" . $tmp . "</option>";
                    }
                    ?>
                    </select><br> 
Delete Current Animal: <input value="delete animal" name="deleteAnimal" type="submit">
Animal:             <select name="animal">
                    <option value="" disabled selected>Select</option>
                    <?php
                    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
                    $animal_list=mysqli_query($connection, "SELECT * FROM FarmItem WHERE IsApproved = 1 AND Type = 'ANIMAL'");
                    while($row = mysqli_fetch_array($animal_list)){
                        $tmp = $row['Name'];
                        echo "<option value='$tmp'>" . $tmp . "</option>";
                    }
                    ?>
                    </select><br>
<?php
}
?> 
Current Crop:     <select name="curCrop">
                    <?php
                    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
                    $curCrop_list=mysqli_query($connection, "SELECT * FROM FarmItem, Has WHERE Has.PropertyID = '$id' AND Has.ItemName = FarmItem.Name AND FarmItem.Type != 'ANIMAL'");
                    while($row = mysqli_fetch_array($curCrop_list)){
                        $tmp = $row['Name'];
                        echo "<option value='$tmp'>" . $tmp . "</option>";
                    }
                    ?>
                    </select><br>
Delete Current Crop: <input value="delete crop" name="deleteCrop" type="submit">
Crop:           <select name="crop">
                    <option value="" disabled selected>Select</option>
                    <?php
                    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
                    $corp_list=mysqli_query($connection, "SELECT * FROM FarmItem WHERE IsApproved = 1 AND (Type = 'FRUIT' OR Type = 'FLOWER' OR Type = 'VEGETABLE' OR Type = 'NUT')");
                    while($row = mysqli_fetch_array($corp_list)){
                        $tmp = $row['Name'];
                        echo "<option value='$tmp'>" . $tmp . "</option>";
                    }
                    // echo "<option value='Fruit'>Fruit</option>";
                    ?>
                </select><br> 
Current isPublic: <input type="text" name="curIsP" value="<?php echo $curP; ?>"><br>
Public?:        <input type="radio" name="isPublic" value="1">Yes
                <input type="radio" name="isPublic" value="0">No<br>
Current isCommercial: <input type="text" name="curIsC" value="<?php echo $curC; ?>"><br>
Commercial?:    <input type="radio" name="isCommercial" value="1">Yes
                <input type="radio" name="isCommercial" value="0">No<br>

<input value="Confirm Changes" name="submit" type="submit">
<input value="delete property" name="delete" type="submit">
<input value="Log Out" type="button" onclick="location.href = 'login.php';" >
</form>
<?php
echo "<a href='confirmed_properties.php'>Back (Don't Save or Confirm)</a><br/>";
?>
</body>
</html>