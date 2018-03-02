<?php
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
        $headers = 'From: fajournal@kieran.pw';
        mail($to, $subject, $txt, $headers);
        die("sql");
      }
      $rows = mysqli_fetch_row($results);

      if (count($rows) == 2) {
        $titles .= ' 
- ' . $profileName . ': <a href="https://furaffinity.net/journal/' . $rows[1] . '/">' . $rows[0] . '</a>';
      } else {
        $titles .= '
- No journal for ' . $profileName . ' found. Use /add first.';
      }
    }
  }

  return 'Newest journal titles:' . $titles . '';
}