<?php
$servername = "localhost";
$username = "localuser";
$password = "KQZ>x@8eyRH?a?},";
$dbname = "crm";
// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
$sql = "SELECT * FROM users WHERE name='lara'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["id"]. " - Name: " . $row["name"]. " <br>";
  }
} else {
  echo "0 results";
}
?>