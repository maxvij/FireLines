<?php
// Create connection
$conn = mysqli_connect("localhost", "root", "");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_select_db($conn,  'firelines');
$last_id = $_GET['lastid'];
$query = "SELECT * FROM Nederland WHERE ID >= " . $last_id;
$result = mysqli_query($conn, $query);

$rows = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
print json_encode($rows);
?>