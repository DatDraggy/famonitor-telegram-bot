<?php
require_once(__DIR__ . "../funcs.php");

$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);

$chatId = preg_replace("/[^0-9]/", "", $argv[1]);
$chatIdQuery = $chatId . '1' . $config['escapeString'];

$sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$chatIdQuery}', '{$chatId}0{$config['escapeString']}') WHERE `email` LIKE '%$chatIdQuery%'";
  $result = mysqli_query($conn, $sql);
  if ($result == false) {
    $to = 'admin@kieran.pw';
    $subject = 'Error Telegram List';
    $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
    $headers = 'From: fajournal@kieran.pw';
    mail($to, $subject, $txt, $headers);
    die("sql");
  }


echo "Removed all users from your monitor list.";