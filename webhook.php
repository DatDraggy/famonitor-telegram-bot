<?php
require_once(__DIR__ . "/../funcs.php");

$response = file_get_contents('php://input');
$data = json_decode($response, true);
$dump = print_r($data, true);

$chatId = sanitizeInputs($data['message']['chat']['id']);
$username = sanitizeInputs($data['message']['chat']['username']);
$message = sanitizeInputs($data['message']['text'], true);

if (substr($message, '0', '1') == '/') {
  $profilesArr = array();
  if (strpos($message, ' ') !== false) {
    $profilesArr = explode(' ', $message);
  }
  else {
    $profilesArr[0] = $message;
    $profilesArr[1] = '';
  }

  $command = $profilesArr[0];
  array_splice($profilesArr, 0, '1');
}
else{
  $command = '/unknown';
}

switch ($command) {
  case "/start":
    //Start

    sendMessage($chatId, '
Hey there, ' . $username . '.

Use /help to get a list of the available commands.

This bot automatically monitors furaffinity.net profiles for new journals. 
Once a new journal has been posted, you will receive a message from the bot.

For support or questions, go poke my daddy @DatDraggy.
');
    die();
    break;
  case "/help":
    //Help

    sendMessage($chatId, '
Here is a small overview of available commands. Alternatively, you can use famonitor.com for email notifications.

You can click add and remove to find out more about their usage.


/add - Adds the specified users to your monitor list

/remove - Removes the specified users from your monitor list

/removeall - Removes all users from your monitor list

/list - Shows all users you\'re currently monitoring

/title - Returns the newest journal title of the specified user 

<code>Text</code> - Indicates a command name
<b>Text</b> - Required parameter
<i>Text</i> - Optional parameter
');
    die();
    break;
  case "/add":
    //Add to mon

    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
Adds a user to your monitor list. You can also specify multiple users seperated by spaces. (Max 4 users)

Usage: <code>/add</code> <b>user1</b> <i>user2</i>

/add Kieran
');
      die();
    }
    else {
      $profiles = '';
      foreach ($profilesArr as $profile) {
        $profiles .= ' ' . $profile;
        if(substr_count($profiles, ' ') == 5){
          break;
        }
      }
      $output = shell_exec('php add.php ' . $chatId . ' ' . $username . $profiles);
      sendMessage($chatId, $output);
    }
    break;
  case "/remove":
    //Remove from mon

    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
Removes a user from your monitor list. You can also specify up to 4 users to remove from your list.

Usage: <code>/remove</code> <b>user1</b> <i>user2</i>

/remove Kieran
');
      die();
    }

    else {
      $profiles = '';
      foreach ($profilesArr as $profile) {
        $profiles .= ' ' . $profile;
        if(substr_count($profiles, ' ') == 5){
          break;
        }
      }
      $output = shell_exec('php remove.php ' . $chatId . ' ' . $profiles);
      sendMessage($chatId, $output);
    }
    break;
  case "/removeall":
    //Removeall

    $output = shell_exec('php removeall.php ' . $chatId);
    sendMessage($chatId, $output);
    die();
    break;
  case "/list":
    //List

    $output = shell_exec('php list.php ' . $chatId);
    sendMessage($chatId, $output);
    die();
    break;
  case "/ping":
    //Pong
    
    sendMessage($chatId, 'Pong.');
    die();
    break;
  case "/title":
    //Return title
    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
Returns the newest titles of profiles that are already being monitored.

Usage: <code>/title</code> <b>user1</b> <i>user2</i>

/title Kieran
');
      die();
    }

    else {
      $profiles = '';
      foreach ($profilesArr as $profile) {
        $profiles .= ' ' . $profile;
        if (substr_count($profiles, ' ') == 5) {
          break;
        }
      }
      $output = shell_exec('php title.php ' . $chatId . ' ' . $profiles);
      sendMessage($chatId, $output);
    }
    break;
  default:
    //Default

    sendMessage($chatId, 'Use /help if you need assistance.');
    die();
    break;
}