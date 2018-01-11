<?php
//$chatIdQuery = '%' . $chatId . '1%+=#..?+#';
// Query: SELECT profile FROM fajournalmon WHERE email like ':chatIdQuery'
//1+=#..?+#
require_once('/var/www/famonitor.com/funcs.php');
$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);

$chatId = preg_replace("/[^0-9]/", "", $argv[1]);
$chatIdQuery = '%' . $chatId . '1%+=#..?+#';
$monlist = 'Monitoring: ';

$sql = "SELECT profile FROM fajournalmon WHERE email like '{$chatIdQuery}'";
$result = mysqli_query($conn, $sql);
if ($result == false) {
  $to = 'admin@kieran.pw';
  $subject = 'Error Telegram List';
  $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
  $headers = 'From: fajournal@kieran.pw';
  mail($to, $subject, $txt, $headers);
  die("sql");
}

if(empty($row)){
  die('You are not monitoring any accounts.');
}

while ($row = $result->fetch_array()) {
  $rows[] = $row;
}

foreach ($rows as $row) {
  $monlist .= $row['profile'];
}

echo $monlist;