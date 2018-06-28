
<?php

require_once './vendor/autoload.php';



setlocale(LC_CTYPE, "en_US.UTF-8");

$channel_token ='channel_token';

if (count($argv) < 2) {
    return;
}

$file = $argv[1];
if (file_exists($file) == false) {
    return;
}

$body = file_get_contents($file);

$json = json_decode($body, true);
if (empty($json)) {
    return;
}

$url = 'https://api.line.me/v2/bot/message/reply';

$headers = [
    'Content-Type:application/json',
    'Authorization: Bearer ' . $channel_token,
];

$http = new \KS\HTTP\HTTP();
$http->setHeaders($headers);
foreach ($json['events'] as $event) {
    if ($event['type'] != 'message') {
        continue;
    }

    $replyToken = $event['replyToken'];
    $message = str_replace("\n", " ", trim($event['message']['text']));


    if($message == "!register")
    {
        $message = $json['events'][0]['source']['userId'];
        $command = "LANG=en_US.UTF-8 PYTHONIOENCODING=utf-8 python ./python/regstaff.py " . escapeshellarg($message) . " 2>&1";
    }
    //Execute python chatbot
    else
    {
        $command = "LANG=en_US.UTF-8 PYTHONIOENCODING=utf-8 python ./python/core.py " . escapeshellarg($message) . " 2>&1";
    }
    $response_message = shell_exec($command);
    $response_message = trim($response_message);

    $post_data = [
        'replyToken' => $replyToken,
        'messages' => [
            ['type' => 'text', 'text' => $response_message],
        ]
    ];
    $response = $http->post($url, json_encode($post_data));
}

//Remove file
unlink($file);
