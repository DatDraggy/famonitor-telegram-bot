<?php
require_once(__DIR__ . "/../funcs.php");

$conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);

//fucc ur injection imma sanitize the hell out of you
$chatId = preg_replace("/[^0-9]/", "", $argv[1]);

for ($i = 2; $i <= $argv; $i++) {
  if (!empty($argv[$i])) {
    $profileName = strtolower($argv[$i]);
    $sql = "SELECT `lastTitle`,`lastId` FROM `fajournalmon` WHERE `profile`='" . $profileName . "'";
    $results = mysqli_query($conn, $sql);
    if ($results == false) {
      $to = 'admin@kieran.pw';
      $subject = 'Error journal check';
      $txt = __FILE__ . ' Error: ' . $sql . '<br>' . mysqli_error($conn);
      $headers = 'From: fajournal@kieran.pw';
      mail($to, $subject, $txt, $headers);
      die("sql");
    }
    $rows = mysqli_fetch_row($results);
    if (count($rows) == 1) {
      $titles .= ' 
- ' . $profileName . ': <a href="https://furaffinity.net/journal/' . $rows[1] . '/">' . $rows[0] . '</a>';
    }
    else {
      $titles .= '
- No journal for ' . $profileName . ' found. Use /add first.';
    }
  }
  else {
    die('Newest journal titles:' . $titles . '');
  }
}

echo 'Added the following users to your monitor list:' . $titles . '';