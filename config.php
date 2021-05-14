<?
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Tashkent');

define('API_KEY','1771542675:AAGls4UDtqbCwDJofmq2NohRR-GX4XVzkAg');
$Manager = "954393349";
$company_name = "Umarbek instagram bot";
$api_url = "https://im-insta-api.herokuapp.com/info?url=";
$logging = false; // false yoki true json fleni yozadi
$channels = [
    [
        'chan_id' => "-1001294548044",
        'btn_text' => "Test kanal 😉",
        'username' => "test_instauchun",
        'required' => 1
    ],
    [
        'chan_id' => "-1001345577282",
        'btn_text' => "One Million Uzbek Coders",
        'username' => "millionuzbekcoders",
        'required' => 0
    ],
    [
        'chan_id' => "-1001418546384",
        'btn_text' => "Najot.uz",
        'username' => "najot_uz_rasmiy",
        'required' => 0
    ]
]; 