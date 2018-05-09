<?php
// Start the session
session_start();
require 'dbinfo.php' ; 
?>
<html>
<title>Owner Registration</title>

<body>

<?php
if(isset($_POST['register'])) { 
    if(strcmp($_POST['password'], $_POST['confirm']) != 0) {
        echo "password not match!";
    } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        echo("Invalid email address");
    }else {
        $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $pslength=strlen($_POST['password']);
        $hashPswd = md5($password);
        
        if($pslength < 8){
            echo "password must have at least 8 characters!";
        }else{
            $query = "INSERT INTO User(Username, Email, Password, UserType)
                        VALUES('$username', '$email', '$hashPswd','OWNER')";
            if(mysqli_query($connection, $query)) {
                echo "Owner '$username' registered successfully; ";
            } else {
                echo "Owner '$username' Registration failed; ";
            }
        }
        
        $property_name = $_POST['property_name'];
        $street_adr = $_POST['street_adr'];
        $city = $_POST['city'];
        $zip = intval($_POST['zip']);
        $acres = (float)$_POST['acres'];
        $property_type = $_POST['propertyType'];
        if(isset($_POST['animal'])){
            $animal = $_POST['animal'];
        }
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
            echo "Property '$property_name' registered successfully\n";
        } else {
            echo "'$id', '$property_name', '$street_adr', '$zip', '$city', '$acres', '$property_type', '$isPublic', '$isCommercial'  \n ";
            echo "Property Registration failed\n";
        }
        if(isset($_POST['animal'])){
            $query3 = "INSERT INTO Has VALUES('$id', '$animal')";
            if(mysqli_query($connection, $query3)){
                echo "Has-animal Update Succeed;";
            }else {
                echo "Has-animla Update failed;";
            }
        }
        if(isset($_POST['corp'])){
            $query5 = "INSERT INTO Has VALUES('$id', '$corp')";
            if(mysqli_query($connection, $query5)){
                echo "Has-corp Update Succeed;";
            }else {
                echo "Has-corp Update failed;";
            }
        }
        
    }
}
?>

<form action="register_owner.php" method="post">
Username: <input type="text" name="username" required><br>
E-mail: <input type="text" name="email" required><br>
Password: <input type="password" name="password" required><br>
Confirm password: <input type="password" name="confirm" required><br>

Property Name: <input type="text" name="property_name" required><br>
Street Address: <input type="text" name="street_adr" required><br>
City: <input type="text" name="city" required><br>
Zip: <input type="text" name="zip" required><br>
Acres: <input type="text" name="acres" required><br>
Property Type:  <select id='propertySelect' name="propertyType" onchange='change()' required>
                    <option value="" disabled selected>Select</option>
                    <?php
                    echo "<option value='Farm'>Farm</option>";
                    echo "<option value='Garden'>Garden</option>";
                    echo "<option value='Orchard'>Orchard</option>";
                    ?>
                </select><br>

Animal: <select id='animalSelect' name="animal">
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

Crop:   <select name="crop">
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
Public?:        <input type="radio" name="isPublic" value="1">Yes
                <input type="radio" name="isPublic" value="0" checked>No<br>
Commercial?:    <input type="radio" name="isCommercial" value="1">Yes
                <input type="radio" name="isCommercial" value="0" checked>No<br>
<input value="Register" type="submit" name="register">
<input value="Cancel" type="button" onclick="location.href = 'login.php'">

</form>

<script>
function change() {
    var x = document.getElementById('propertySelect').value;
    if(x=='Farm'){
        document.getElementById('animalSelect').disabled = false;
    } else {
        document.getElementById('animalSelect').disabled = true;
    }
}
</script>

</body>
</html>