<?php
function famon_remove($errorMail, $conn, $chatId, $profilesArr, $escapeString) {
  $email = $chatId;
  $removed = '';

  foreach ($profilesArr as $profileName) {
    if (!empty($profileName)) {
      $sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$email}1{$escapeString}', '{$email}0{$escapeString}') WHERE `profile`='{$profileName}'";
      $results = mysqli_query($conn, $sql);
      if ($results == false) {
        $to = $errorMail;
        $subject = 'Error journal check';
        $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
        $headers = 'From: fajournal@kieran.de';
        mail($to, $subject, $txt, $headers);
        die("Something went wrong! The admin has been notified.");
      }
      $removed .= ' 
- ' . $profileName;
    }
  }

  return "Removed following users from your monitor list:" . $removed;
}