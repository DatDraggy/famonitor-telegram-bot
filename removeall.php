<?php
function famon_removeall($errorMail, $conn, $chatId, $escapeString) {
  $chatIdQuery = $chatId . '1' . $escapeString;

  $sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$chatIdQuery}', '{$chatId}0{$escapeString}') WHERE `email` LIKE '%{$chatIdQuery}%'";
  $result = mysqli_query($conn, $sql);
  if ($result == false) {
    $to = $errorMail;
    $subject = 'Error Telegram List';
    $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
    $headers = 'From: fajournal@kieran.pw';
    mail($to, $subject, $txt, $headers);
    die();
  }

  return "Removed all users from your monitor list.";
}