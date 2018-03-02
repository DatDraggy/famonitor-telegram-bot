<?php
require_once(__DIR__ . "/../funcs.php");

$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);

$chatId = preg_replace("/[^0-9]/", "", $argv[1]);
$name = preg_replace("/[^\w]/", '', $argv[2]);
$chatIdQuery = $chatId . '1' . $config['escapeString'];
$added = '';
$email = $chatId;
$ip = 'telegram';
$current_date = date('Y-m-d H:i:s');

for ($i = 3; $i <= $argv; $i++) {
  if (!empty($argv[$i])) {
    $profileName = strtolower($argv[$i]);
    $sql = "SELECT `email` FROM `fajournalmon` WHERE `profile`='" . $profileName . "'";
    $results = mysqli_query($conn, $sql);
    if ($results == false) {
      $to = 'admin@kieran.pw';
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
        if (strpos($rows[0], $email . '0' . $config['escapeString']) !== false) {
          $sql = "UPDATE `fajournalmon` SET `email`=REPLACE(`email`, '{$email}0{$config['escapeString']}', '{$email}1{$config['escapeString']}') WHERE `profile`='{$profileName}'";
          $results = mysqli_query($conn, $sql);
          if ($results == false) {
            $to = 'admin@kieran.pw';
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
      $sql = "UPDATE `fajournalmon` SET `name`=CONCAT(name, '" . $name . "|'),`email`= CONCAT(email, '" . $email . '1' . $config['escapeString'] . "'),`time`= CONCAT(time, '" . $current_date . "|'),`ip`= CONCAT(ip, '" . $ip . "|') WHERE `profile`='" . $profileName . "'";
      $results = mysqli_query($conn, $sql);
      if ($results == false) {
        $to = 'admin@kieran.pw';
        $subject = 'Error journal check';
        $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
        $headers = 'From: fajournal@kieran.pw';
        mail($to, $subject, $txt, $headers);
        die("sql");
      }
    }
    else {
      $sql = "SELECT MAX(id),`a`,`b` FROM `fajournalmon_cookies` WHERE `a` <> ''";
      $results = mysqli_query($conn, $sql);
      $rows = mysqli_fetch_row($results);

      $newest = checkTitle($rows[1], $rows[2], $profileName);
      $sql = "INSERT INTO `fajournalmon`(`profile`, `name`, `email`, `lastTitle`, `time`, `ip`, `lastId`) VALUES ('{$profileName}', '{$name}|', '{$email}1{$config['escapeString']}', '{$newest[0]}','{$current_date}|','{$ip}|', '{$newest[1]}')";
      $results = mysqli_query($conn, $sql);
      if ($results == false) {
        $to = 'admin@kieran.pw';
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
  else {
    die('Added the following users to your monitor list:' . $added . '');
  }
}

echo 'Added the following users to your monitor list:' . $added . '';