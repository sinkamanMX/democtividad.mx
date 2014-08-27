<?php
error_reporting(E_ALL);
/*$connect=mysql_connect("201.131.96.45:3306","dba","t3cnod8A!");

if (!$connect){
  die('Could not connect: ' . mysql_error());
}


mysql_close($connect);
*/

$con = mysqli_connect("201.131.96.45","dba","t3cnod8A!","SIMA");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

  echo "SE CONECTo";
?>