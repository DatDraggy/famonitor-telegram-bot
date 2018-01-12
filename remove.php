<?php
require_once (__DIR__ . '/../funcs.php');
$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);
$chatId = preg_replace("/[^0-9]/", "", $argv[1]);
$email = $chatId;
$removed = '';

for ($i = 2; $i <= $argv; $i++) {
  if (!empty($argv[$i])) {
    $profileName = strtolower($argv[$i]);
    $sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$email}1{$config['escapeString']}', '{$email}0{$config['escapeString']}') WHERE `profile`='{$profileName}'";
    $results = mysqli_query($conn, $sql);
    if ($results == false) {
      $to = 'admin@kieran.pw';
      $subject = 'Error journal check';
      $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
      $headers = 'From: fajournal@kieran.pw';
      mail($to, $subject, $txt, $headers);
      die("Something went wrong! The admin has been notified.");
    }

    $removed .= ' <b>' . $profileName . '</b>';
  }
  else{
    die("Removed following users from your monitor list:" . $removed);
  }
}