<?php

$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);

$email = $chatId;
$removed = '';

foreach($profilesArr as $profileName){
  if (!empty($profileName)) {
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

    $removed .= ' 
- ' . $profileName;
  }
}

$output = "Removed following users from your monitor list:" . $removed;