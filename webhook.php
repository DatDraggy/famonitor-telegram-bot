<?php

function sendMessage($chatId, $text) {
  //Yes, my API key is here on purpose. I will revoke it later on when the bot is finished since nobody is using it atm.
  $url = 'https://api.telegram.org/bot547541749:AAH7xydiov_Fgt0crsHcFNJ0GiOhydoI1Qg/';
  $response = file_get_contents($url . 'sendMessage?parse_mode=html&chat_id=' . $chatId . '&text=' . urlencode($text));
  if (empty($response)) {
    //Blocked by user probably. Remove from DB later
  }
}

$response = file_get_contents('php://input');
$data = json_decode($response, true);
$dump = print_r($data, true);

$chatId = $data['message']['chat']['id'];
$username = $data['message']['chat']['username'];
$firstName = $data['message']['chat']['first_name'];
$message = $data['message']['text'];

if (substr($message, '0', '1') == '/') {
  $messageArr = array();
  if (strpos($message, ' ') !== false) {
    $messageArr = explode(' ', $message);
  }
  else {
    $messageArr[0] = $message;
    $messageArr[1] = '';
  }

  $command = $messageArr[0];
}

if ($command == '/start') {
  //Start

  sendMessage($chatId, '
Hey there, ' . $firstName . '/' . $username . '.

Use /help to get a list of the available commands.

This bot automatically monitors furaffinity.net profiles for new journals. 
Once a new journal has been posted, you will receive a message from the bot.

For support or questions, go poke my daddy @DatDraggy.
');
  die();
}

elseif ($command == '/help') {
  //Help

  sendMessage($chatId, '
Here is a small overview of available commands. Alternatively, you can use famonitor.com for email notifications.

You can click add and remove to find out more about their usage.


/add - Adds the specified users to your monitor list <b>[In development]</b>

/remove - Removes the specified users from your monitor list <b>[In development]</b>

/removeall - Removes all users from your monitor list

/list - Shows all users you\'re currently monitoring

<code>Text</code> - Indicates a command name
<b>Text</b> - Required parameter
<i>Text</i> - Optional parameter
');
  die();
}

elseif ($command == '/add') {
  //Add to mon

  if (empty($messageArr[1])) {
    sendMessage($chatId, '
Adds a user to your monitor list. You can also specify multiple users seperated by spaces. (Max 4 users)


Usage: <code>/add</code> <b>user1</b> <i>user2</i>

/add Kieran
');
    die();
  }
  else {
    $count = 0;
    $added = 'Added to monitor list:';
    foreach ($messageArr as $parameter) {
      if (strpos($parameter, '/') === false && $count < 4) {
        $count += 1;
        $output = shell_exec('php add.php ' . $parameter . ' ' . $username . ' ' . $chatId);
        $added = $added . ' ' . $parameter;
        sleep(1);
        //Cuz we don't wanna screw sometin up eh? uwu
      }
    }
    sendMessage($chatId, $added);
  }
}

elseif ($command == '/remove') {
  //Remove from mon

  if (empty($messageArr[1])) {
    sendMessage($chatId, '
Removes a user from your monitor list. You can also specify up to 4 users to remove from your list.

Usage: <code>/remove</code> <b>user1</b> <i>user2</i>

/remove Kieran
');
    die();
  }
  else {
    $count = 0;
    $removed = 'Removed from monitor list:';
    foreach ($messageArr as $parameter) {
      if (strpos($parameter, '/') === false && $count < 4) {
        $count += 1;
        $output = shell_exec('php remove.php ' . $parameter . ' ' . $chatId);
        $removed = $removed . ' ' . $parameter;
        sleep(1);
      }
    }
    sendMessage($chatId, $removed);
  }
}


elseif ($command == '/removeall') {
  //Removeall

  $output = shell_exec('php removeall.php ' . $chatId);
  sendMessage($chatId, $output);
  die();
}

elseif ($command == '/list') {
  //List

  $output = shell_exec('php list.php ' . $chatId);
  sendMessage($chatId, $output);
  die();
}

//Test