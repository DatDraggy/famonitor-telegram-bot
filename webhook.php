<?php
require_once(__DIR__ . "/../funcs.php");
require_once(__DIR__ . "/methods.php");

$response = file_get_contents('php://input');
$data = json_decode($response, true);
$dump = print_r($data, true);

$chatId = sanitizeInputs($data['message']['chat']['id']);
if(isset($data['message']['chat']['username'])) {
  $username = sanitizeInputs($data['message']['chat']['username']);
}
else{
  $username = sanitizeInputs($data['message']['chat']['first_name'], true);
  if(isset($data['message']['chat']['last_name'])){
    $username = $username . ' ' . sanitizeInputs($data['message']['chat']['last_name'], true);
  }
}
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
else {
  $command = '/unknown';
}

switch ($command) {
  case '/start':
    //Start

    sendMessage($chatId, '
Hey there, ' . $username . '.

Use /help to get a list of the available commands.

This bot automatically monitors furaffinity.net profiles for new journals. 
Once a new journal has been posted, you will receive a message from the bot.

Make sure to use the username in the profile url when adding or removing users.
https://furaffinity.net/user/<b>kieran</b>/

For support or questions, go poke @DatDraggy.
');
    die();
    break;
  case '/help':
    //Help

    sendMessage($chatId, '
Here is a small overview of available commands. Alternatively, you can use famonitor.com for email notifications.

You can click add, remove and title to find out more about their usage.


/add - Adds the specified users to your monitor list

/remove - Removes the specified users from your monitor list

/removeall - Removes all users from your monitor list

/list - Shows all users you\'re currently monitoring

/title - Returns the newest journal title of the specified user 

<code>/addpage</code> - [Disabled] Experimental commission state check on profile page

/id - Returns the current chat id

/ping - ...?

<code>Text</code> - Indicates a command name
<b>Text</b> - Required parameter
<i>Text</i> - Optional parameter
');
    die();
    break;
  case '/add':
    //Add to mon

    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
Adds a user to your monitor list. You can also specify multiple users seperated by spaces. (Max 4 users)

Usage: <code>/add</code> <b>username1</b> <i>username2</i>

/add Kieran
');
      die();
    }
    else {
      $conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);
      $output = famon_add($config['email'], $conn, $username, $chatId, $profilesArr, $config['escapeString']);
      sendMessage($chatId, $output);
    }
    break;
  case '/remove':
    //Remove from mon

    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
Removes a user from your monitor list. You can also specify up to 4 users to remove from your list.

Usage: <code>/remove</code> <b>username1</b> <i>username2</i>

/remove Kieran
');
      die();
    }
    else {
      $conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);
      $output = famon_remove($config['email'], $conn, $chatId, $profilesArr, $config['escapeString']);
      sendMessage($chatId, $output);
    }
    break;
  case '/removeall':
    //Removeall

    $conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);
    $output = famon_removeall($config['email'], $conn, $chatId, $config['escapeString']);
    sendMessage($chatId, $output);
    die();
    break;
  case '/list':
    //List

    $conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);
    $output = famon_list($config['email'], $conn, $chatId, $config['escapeString']);
    sendMessage($chatId, $output);
    die();
    break;
  case '/ping':
    //Pong

    sendMessage($chatId, 'Pong.');
    die();
    break;
  case '/title':
    //Return title

    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
Returns the newest titles of profiles that are already being monitored.

Usage: <code>/title</code> <b>username1</b> <i>username1</i>

/title Kieran
');
      die();
    }
    else {
      $conn = new mysqli($config['server'], $config['user'], $config['password'], $config['database']);
      $output = famon_title($config['email'], $conn, $profilesArr);
      sendMessage($chatId, $output);
    }
    break;
  case '/addpage':
    //Add Page

    //In Check if # first char of profile do text compare of profile text in lastTitle
    if (empty($profilesArr[0])) {
      sendMessage($chatId, '
<b>Experimental</b>
Adds a user to your list and uses the specified text to find the profile\'s commission status on their user page.
Put the text before the commission state in double quotation marks and then the state you want to receive notifications for after.

Usage: <code>/addpage</code> <b>username1</b> <b>"state text"</b> <b>state</b>
Screenshot: https://puu.sh/zA09S/cc4118bb4f.png

/addpage Kieran "Example Text:" open
');
    }
    else {

    }
    break;
  case '/id':
    //Echo chatId

    sendMessage($chatId, $chatId);
    break;
  default:
    //Default

    sendMessage($chatId, 'Unknown command! Use /help if you need assistance or contact @DatDraggy.');
    die();
    break;
}
