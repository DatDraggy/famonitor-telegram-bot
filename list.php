<?php
//$chatIdQuery = '%' . $chatId . '1%+=#..?+#';
// Query: SELECT profile FROM fajournalmon WHERE email like ':chatIdQuery'
//1+=#..?+#
require_once(__DIR__ . "/../funcs.php");

$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);

$chatId = preg_replace("/[^0-9]/", "", $argv[1]);
$chatIdQuery = $chatId . '1' . $config['escapeString'];
$monlist = 'Monitoring: ';

$sql = "SELECT `profile` FROM `fajournalmon` WHERE `email` LIKE '%{$chatIdQuery}%'";
$result = mysqli_query($conn, $sql);
if ($result == false) {
  $to = 'admin@kieran.pw';
  $subject = 'Error Telegram List';
  $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
  $headers = 'From: fajournal@kieran.pw';
  mail($to, $subject, $txt, $headers);
  die("sql");
}

while ($row = $result->fetch_array()) {
  $rows[] = $row;
}

if (empty($rows)) {
  die('You are not monitoring any accounts.');
}

foreach ($rows as $row) {
  $monlist .= ' 

- <a href="https://furaffinity.net/user/' . $row['profile'] . '">' . $row['profile'] . '</a>';
}

echo $monlist;