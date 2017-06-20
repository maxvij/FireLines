<?php
// Create connection
$conn = mysqli_connect("148.251.187.171", "sierdmei", "Xhw23B01ms");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_select_db($conn,  'sierdmei_p2000');
$last_id = $_GET['lastid'];
$query = "SELECT * FROM Nederland WHERE ID > " . $last_id;
$result = mysqli_query($conn, $query);

$rows = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
if(sizeof($rows) > 0) {
    print json_encode($rows);
} else {
    print 'UP-TO-DATE';
}
?>