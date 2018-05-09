<?php
// Start the session
session_start();
require 'dbinfo.php' ; 
?>  
<html>
<title>Visitor Property Logged Page</title>
<body>

<?php
    $PropertyID = $_GET['ID'];
    $username = $_GET['username'];
    $date = date('Y-m-d H:i:s');
    $connection = mysqli_connect($host, $usernameDB, $passwordDB, $database) or die( "Unable to connect");      
    
    if(isset($_GET['rate'])){
        $rate = $_GET['rate'];
        $query6 = "INSERT INTO Visit VALUES ('$username','$PropertyID','$date','$rate')";
        mysqli_query($connection, $query6);
    }
    
    $query1 = "SELECT * FROM Property WHERE ID = '$PropertyID'";
    $query2 = "SELECT Avg(Rating) FROM Visit WHERE PropertyID = '$PropertyID'";
    $cursor = mysqli_query($connection, $query1);
    $cursor2 = mysqli_query($connection, $query2);
    $row = mysqli_fetch_row($cursor);
    $row2 = mysqli_fetch_row($cursor2);
    $query3 = "SELECT Email FROM User WHERE Username = '$row[9]'";
    $cursor3 = mysqli_query($connection, $query3);
    $row3 = mysqli_fetch_row($cursor3);
    $query4 = "SELECT Has.ItemName FROM Has INNER JOIN FarmItem on Has.ItemName=FarmItem.Name WHERE TYPE!='ANIMAL' AND PropertyID='$PropertyID'";
    $temp_query4 = mysqli_query($connection, $query4);
    $query5 = "SELECT Has.ItemName FROM Has INNER JOIN FarmItem on Has.ItemName=FarmItem.Name WHERE TYPE='ANIMAL' AND PropertyID='$PropertyID'";
    $temp_query5 = mysqli_query($connection, $query5);
?>

<h2><?php echo $row[1];?></h2>
<table style="width:100%">
<tr>
Name: <?php echo $row[1];?><br>
Owner: <?php echo $row[9];?><br>
Owner Email: <?php echo $row3[0];?><br>
Visits: <?php echo $row[0];?><br>
Address: <?php echo $row[5];?><br>
City: <?php echo $row[6];?><br>
Zip: <?php echo $row[7];?><br>
Size(scres): <?php echo $row[2];?><br>
Avg.Rating: <?php echo $row2[0];?><br>
Type: <?php echo $row[8];?><br>
Public: <?php echo $row[4];?><br>
Commercial: <?php echo $row[3];?><br>
ID: <?php echo $row[0];?><br>
Crops: <?php 
    while($row4 = mysqli_fetch_row($temp_query4)) {
        echo $row4[0]. " ";
    }
;?><br>
<?php
if($temp_query5&&mysqli_num_rows($temp_query5) > 0){
    echo "Animals: ";
    while($row5 = mysqli_fetch_row($temp_query5)) {
        echo $row5[0]. " ";
    }
}
?><br><br>
<?php
echo "<a href='visitor_property_page.php?ID=".$PropertyID."&username=".$username."&UnLog=1'>Un-Log Visit</a></td>";
?>
<br>
</tr>

<?php
echo "<a href='visitor_main_page.php?name=".$_GET["username"]."'>Back</a><br/>";
?>

</body>
</html>