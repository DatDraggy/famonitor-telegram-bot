<?php
function famon_list($errorMail, $conn, $chatId, $escapeString) {
  $chatIdQuery = $chatId . '1' . $escapeString;
  $monlist = 'Monitoring: ';

  $sql = "SELECT `profile` FROM `fajournalmon` WHERE `email` LIKE '%{$chatIdQuery}%'";
  $result = mysqli_query($conn, $sql);
  if ($result == false) {
    $to = $errorMail;
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
    return 'You are not monitoring any accounts.';
  }

  foreach ($rows as $row) {
    $monlist .= ' 
- <a href="https://furaffinity.net/user/' . $row['profile'] . '">' . $row['profile'] . '</a>';
  }

  return $monlist;
}