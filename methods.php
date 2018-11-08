<?php
function famon_add($errorMail, $conn, $username, $chatId, $profilesArr, $escapeString) {
  $chatIdQuery = $chatId . '1' . $escapeString;
  $added = '';
  $email = $chatId;
  $ip = 'telegram';
  $current_date = date('Y-m-d H:i:s');

  foreach ($profilesArr as $profileName) {
    if (!empty($profileName)) {
      $sql = "SELECT `email` FROM `fajournalmon` WHERE `profile`='" . $profileName . "'";
      $results = mysqli_query($conn, $sql);
      if ($results == false) {
        $to = $errorMail;
        $subject = 'Error journal check';
        $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
        $headers = 'From: fajournal@kieran.de';
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
              $headers = 'From: fajournal@kieran.de';
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
          $headers = 'From: fajournal@kieran.de';
          mail($to, $subject, $txt, $headers);
          die("sql");
        }
      }
      else {
        $sql = "SELECT MAX(id),`a`,`b` FROM `fajournalmon_cookies` WHERE `a` <> ''";
        $results = mysqli_query($conn, $sql);
        $rows = mysqli_fetch_row($results);

        //list($newestTitle, $newestId) = checkTitle($rows[1], $rows[2], $profileName);
        $newestId = -1;
        $newestTitle = 'Refreshing...';
        $sql = "INSERT INTO `fajournalmon`(`profile`, `name`, `email`, `lastTitle`, `time`, `ip`, `lastId`) VALUES ('{$profileName}', '{$username}|', '{$chatIdQuery}', '{$newestTitle}','{$current_date}|','{$ip}|', '{$newestId}')";
        $results = mysqli_query($conn, $sql);
        if ($results == false) {
          $to = $errorMail;
          $subject = 'Error journal check';
          $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
          $headers = 'From: fajournal@kieran.de';
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

function famon_list($errorMail, $conn, $chatId, $escapeString) {
  $chatIdQuery = $chatId . '1' . $escapeString;
  $monlist = 'Monitoring: ';

  $sql = "SELECT `profile` FROM `fajournalmon` WHERE `email` LIKE '%{$chatIdQuery}%'";
  $result = mysqli_query($conn, $sql);
  if ($result == false) {
    $to = $errorMail;
    $subject = 'Error Telegram List';
    $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
    $headers = 'From: fajournal@kieran.de';
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

function famon_removeall($errorMail, $conn, $chatId, $escapeString) {
  $chatIdQuery = $chatId . '1' . $escapeString;

  $sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$chatIdQuery}', '{$chatId}0{$escapeString}') WHERE `email` LIKE '%{$chatIdQuery}%'";
  $result = mysqli_query($conn, $sql);
  if ($result == false) {
    $to = $errorMail;
    $subject = 'Error Telegram List';
    $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
    $headers = 'From: fajournal@kieran.de';
    mail($to, $subject, $txt, $headers);
    die();
  }

  return "Removed all users from your monitor list.";
}

function famon_title($errorMail, $conn, $profilesArr) {
  $titles = '';

  foreach ($profilesArr as $profileName) {
    if (!empty($profileName)) {
      $sql = "SELECT `lastTitle`,`lastId` FROM `fajournalmon` WHERE `profile`='" . $profileName . "'";
      $results = mysqli_query($conn, $sql);
      if ($results == false) {
        $to = $errorMail;
        $subject = 'Error journal check';
        $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
        $headers = 'From: fajournal@kieran.de';
        mail($to, $subject, $txt, $headers);
        die("sql");
      }
      $rows = mysqli_fetch_row($results);

      if (count($rows) == 2) {
        $titles .= ' 
- ' . $profileName . ': <a href="https://furaffinity.net/journal/' . $rows[1] . '/">' . $rows[0] . '</a>';
      }
      else {
        $titles .= '
- No journal for ' . $profileName . ' found. Use /add first.';
      }
    }
  }

  return 'Newest journal titles:' . $titles . '';
}