<?php
function famon_add($errorMail, $conn, $username, $chatId, $profilesArr, $escapeString) {
  $chatIdQuery = $chatId . '1' . $escapeString;
  $added = '';
  $email = $chatId;
  $ip = 'telegram';
  $current_date = date('Y-m-d H:i:s');

  foreach ($profilesArr as $profileN) {
    $profileName = strtolower($profileN);
    if (!empty($profileName)) {
      $sql = "SELECT `email` FROM `fajournalmon` WHERE `profile`='" . $profileName . "'";
      $results = mysqli_query($conn, $sql);
      if ($results == false) {
        $to = $errorMail;
        $subject = 'Error journal check';
        $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
        $headers = 'From: fajournal@kieran.pw';
        mail($to, $subject, $txt, $headers);
        die("sql");
      }
      $profileExists = 0;
      $rows = mysqli_fetch_row($results);
      if (count($rows) == 1) {
        $profileExists = 1;
        if (strpos($rows[0], $email) !== false) {
          if (strpos($rows[0], $email . '0' . $escapeString) !== false) {
            $sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$email}0{$escapeString}', '{$chatIdQuery}') WHERE `profile`='{$profileName}'";
            $results = mysqli_query($conn, $sql);
            if ($results == false) {
              $to = $errorMail;
              $subject = 'Error journal check';
              $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
              $headers = 'From: fajournal@kieran.pw';
              mail($to, $subject, $txt, $headers);
              die("sql");
            }
          }
          $added .= ' 
- ' . $profileName;
          continue;
        }
      }
      if ($profileExists) {
        $sql = "UPDATE `fajournalmon` SET `name`=CONCAT(name, '" . $username . "|'),`email`= CONCAT(email, '{$chatIdQuery}'),`time`= CONCAT(time, '" . $current_date . "|'),`ip`= CONCAT(ip, '" . $ip . "|') WHERE `profile`='" . $profileName . "'";
        $results = mysqli_query($conn, $sql);
        if ($results == false) {
          $to = $errorMail;
          $subject = 'Error journal check';
          $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
          $headers = 'From: fajournal@kieran.pw';
          mail($to, $subject, $txt, $headers);
          die("sql");
        }
      } else {
        $sql = "SELECT MAX(id),`a`,`b` FROM `fajournalmon_cookies` WHERE `a` <> ''";
        $results = mysqli_query($conn, $sql);
        $rows = mysqli_fetch_row($results);

        //list($newestTitle, $newestId) = checkTitle($rows[1], $rows[2], $profileName);
        $newestId = 1;
        $newestTitle = 'x';
        $sql = "INSERT INTO `fajournalmon`(`profile`, `name`, `email`, `lastTitle`, `time`, `ip`, `lastId`) VALUES ('{$profileName}', '{$username}|', '{$chatIdQuery}', '{$newestTitle}','{$current_date}|','{$ip}|', '{$newestId}')";
        $results = mysqli_query($conn, $sql);
        if ($results == false) {
          $to = $errorMail;
          $subject = 'Error journal check';
          $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
          $headers = 'From: fajournal@kieran.pw';
          mail($to, $subject, $txt, $headers);
          die("sql");
        }
      }

      $added .= ' 
- ' . $profileName;
    }
  }

  return 'Added the following users to your monitor list:' . $added . '';
}
