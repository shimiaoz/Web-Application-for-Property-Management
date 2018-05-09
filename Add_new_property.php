<?php
// Start the session
session_start();
require 'dbinfo.php' ; 
?>
<html>
<title>Add New Property</title>
<body>


<?php
$username = $_GET['name'];
//echo "Got username: '$username'";
if(isset($_POST['submit'])) {
        $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
        
        $property_name = $_POST['property_name'];
        $street_adr = $_POST['street_adr'];
        $city = $_POST['city'];
        $zip = intval($_POST['zip']);
        $acres = (float)$_POST['acres'];
        $property_type = $_POST['propertyType'];
        $animal = $_POST['animal'];
        $crop = $_POST['crop'];
        $isPublic = $_POST['isPublic'];
        $isCommercial = $_POST['isCommercial'];
        $query = "SELECT * FROM Property";
        $result = mysqli_query($connection, $query);
        $id = mysqli_num_rows($result) + 1;
        
        //(ID, Name, Size, IsCommercial, IsPublic, Street, City, Zip, ProprtyType, Owner, ApprovedBy)
        $query = "INSERT INTO Property VALUES('$id', '$property_name', '$acres','$isCommercial', '$isPublic', '$street_adr', '$city', '$zip', '$property_type', '$username', NULL)";
        //$result
        if(mysqli_query($connection, $query)) {
            echo "'$id', '$property_name', '$username'  \n ";
            echo "Property '$property_name' registered successfully\n";
        } else {
            echo "'$id', '$property_name', '$street_adr', '$zip', '$city', '$acres', '$property_type', '$isPublic', '$isCommercial'  \n ";
            echo "Property Registration failed\n";
        }
    
}
?>

<form  method="post">

Property Name: <input type="text" name="property_name"><br>
Street Address: <input type="text" name="street_adr"><br>
City: <input type="text" name="city"><br>
Zip: <input type="text" name="zip"><br>
Acres: <input type="text" name="acres"><br>
Property Type:  <select id='propertySelect' name="propertyType" onchange='change()' required>
                    <option value="" disabled selected>Select</option>
                    <?php
                    echo "<option value='Farm'>Farm</option>";
                    echo "<option value='Garden'>Garden</option>";
                    echo "<option value='Orchard'>Orchard</option>";
                    ?>
                </select><br>

Animal:     <select required id='animalSelect' name="animal">
                <option value="" disabled selected>Select</option>
                <?php
                $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
                $animal_list = mysqli_query($connection, "SELECT * FROM FarmItem WHERE IsApproved = 1 AND Type = 'ANIMAL'");
                while($row = mysqli_fetch_array($animal_list)){
                    $tmp = $row['Name'];
                    echo "<option value='$tmp'>" . $tmp . "</option>";
                }
                ?>
            </select><br>

All Crops:      <select required id='AllCropSelect' name='crop'>
                    <option value='' disabled selected>Select</option>
                    <?php
                    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die('Unable to connect');
                    $crop_query = "SELECT * FROM FarmItem WHERE IsApproved = 1 AND Type != 'ANIMAL'";
                    $crop_list = mysqli_query($connection, $crop_query);
                    while($row = mysqli_fetch_array($crop_list)){
                        $tmp = $row['Name'];
                        echo "<option value='$tmp'>" . $tmp . '</option>';
                    }
                    ?>
                </select>
Flowers or Vegetables:      <select required id='FVSelect' name='crop'>
                                <option value='' disabled selected>Select</option>
                                <?php
                                $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die('Unable to connect');
                                $crop_query = "SELECT * FROM FarmItem WHERE IsApproved = 1 AND (Type = 'FLOWER' OR Type = 'VEGETABLE')";
                                $crop_list = mysqli_query($connection, $crop_query);
                                while($row = mysqli_fetch_array($crop_list)){
                                    $tmp = $row['Name'];
                                    echo "<option value='$tmp'>" . $tmp . '</option>';
                                }
                                ?>
                            </select>
Fruits or Nuts:    <select required id='FNSelect' name='crop'>
                        <option value='' disabled selected>Select</option>
                        <?php
                        $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die('Unable to connect');
                        $crop_query = "SELECT * FROM FarmItem WHERE IsApproved = 1 AND (Type = 'FRUIT' OR Type = 'NUT')";
                        $crop_list = mysqli_query($connection, $crop_query);
                        while($row = mysqli_fetch_array($crop_list)){
                            $tmp = $row['Name'];
                            echo "<option value='$tmp'>" . $tmp . '</option>';
                        }
                        ?>
                    </select>
<br>

Public?:        <input type="radio" name="isPublic" value="1">Yes
                <input type="radio" name="isPublic" value="0" checked>No<br>
Commercial?:    <input type="radio" name="isCommercial" value="1">Yes
                <input type="radio" name="isCommercial" value="0" checked>No<br>
<input value="Add Property" name="submit" type="submit">
<input value="Cancel" type="button" onclick="location.href = 'owner_function.php/?email=<?php echo $_SESSION['email_view_others'];?>';" >
</form>

<script>
function change() {
    var x = document.getElementById('propertySelect').value;
    if(x=='Farm'){
        document.getElementById('animalSelect').disabled = false;
        document.getElementById('AllCropSelect').disabled = false;
        document.getElementById('FVSelect').disabled = true;
        document.getElementById('FNSelect').disabled = true;
    } else if(x=='Garden') {
        document.getElementById('animalSelect').disabled = true;
        document.getElementById('AllCropSelect').disabled = true;
        document.getElementById('FVSelect').disabled = false;
        document.getElementById('FNSelect').disabled = true;
    } else if(x=='Orchard') {
        document.getElementById('animalSelect').disabled = true;
        document.getElementById('AllCropSelect').disabled = true;
        document.getElementById('FVSelect').disabled = true;
        document.getElementById('FNSelect').disabled = false;
        }
}
</script>

</body>
</html>