<?php
require 'ClassAutoLoad.php';

$mailCnt = [
    'name_from' => 'Mohamedek Aden',
    'mail_from' => 'mohamedek.yussuf@strathmore.edu',
    'name_to' => 'Hanif',
    'mail_to' => 'mohamedekaden576@gmail.com',
    'subject' => 'Hello From Deks Hostels',
    'body' => 'Welcome to Deks Hostels! <br> This is a new year. Let\'s have a wonderful year together.'
];

$ObjSendMail->Send_Mail($conf, $mailCnt);