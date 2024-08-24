<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the EMPLOYEES table exists. */
  VerifyEmployeesTable($connection, DB_DATABASE);
  VerifyOrdersTable($connection, DB_DATABASE);


  /* If input fields are populated, add a row to the EMPLOYEES table. */
  $employee_name = htmlentities($_POST['NAME']);
  $employee_address = htmlentities($_POST['ADDRESS']);

  if (strlen($employee_name) || strlen($employee_address)) {
    AddEmployee($connection, $employee_name, $employee_address);
  }

  $order_userId = htmlentities($_POST['USERID']);
  $order_productName = htmlentities($_POST['PRODUCTNAME']);
  $order_price = htmlentities($_POST['PRICE']);
  $order_orderDate = htmlentities($_POST['ORDERDATE']);
  $order_estimatedArrival = htmlentities($_POST['ORDERESTIMATEDARRIVAL']);

  if (strlen($order_userId) || strlen($order_productName) || strlen($order_price) || strlen($order_orderDate) || strlen($order_estimatedArrival)) {
    AddOrder($connection, $order_userId, $order_productName, $order_price, $order_orderDate, $order_estimatedArrival);
  }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>ADDRESS</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>

<!-- Input form for orders -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>User ID</td>
      <td>Product Name</td>
      <td>Price</td>
      <td>Order Date</td>
      <td>Estimated Arrival</td>
    </tr>
    <tr>
      <td><input type="text" name="USERID" maxlength="11" size="10" /></td>
      <td><input type="text" name="PRODUCTNAME" maxlength="100" size="30" /></td>
      <td><input type="text" name="PRICE" maxlength="10" size="10" /></td>
      <td><input type="date" name="ORDERDATE" /></td>
      <td><input type="date" name="ORDERESTIMATEDARRIVAL" /></td>
      <td><input type="submit" value="Add Order" /></td>
    </tr>
  </table>
</form>


<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>ADDRESS</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Display orders table data. -->
<br>
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>User ID</td>
    <td>Product Name</td>
    <td>Price</td>
    <td>Order Date</td>
    <td>Estimated Arrival</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM ORDERS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>", $query_data[0], "</td>",
       "<td>", $query_data[1], "</td>",
       "<td>", $query_data[2], "</td>",
       "<td>", $query_data[3], "</td>",
       "<td>", $query_data[4], "</td>",
       "<td>", $query_data[5], "</td>";
  echo "</tr>";
}
?>

</table>


<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}


function AddOrder($connection, $userId, $productName, $price, $orderDate, $orderEstimatedArrival) {
   $uid = mysqli_real_escape_string($connection, $userId);
   $pn = mysqli_real_escape_string($connection, $productName);
   $p = mysqli_real_escape_string($connection, $price);
   $od = mysqli_real_escape_string($connection, $orderDate);
   $oea = mysqli_real_escape_string($connection, $orderEstimatedArrival);

   $query = "INSERT INTO ORDERS (USERID, PRODUCTNAME, PRICE, ORDERDATE, ORDERESTIMATEDARRIVAL)
             VALUES ('$uid', '$pn', '$p', '$od', '$oea');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding order data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}


function VerifyOrdersTable($connection, $dbName) {
  if(!TableExists("ORDERS", $connection, $dbName))
  {
     $query = "CREATE TABLE ORDERS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	 USERID int(11) UNSIGNED,
         PRODUCTNAME VARCHAR(100),
	 PRICE DECIMAL (10, 2),
	 ORDERDATE DATE,
	 ORDERESTIMATEDARRIVAL DATE
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
