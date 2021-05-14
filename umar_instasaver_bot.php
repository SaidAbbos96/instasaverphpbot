<? 
require_once "config.php";
require_once "funcs.php";

if(isset($_GET['reset'])){
    $protokol = $_SERVER['REQUEST_SCHEME'];
    if($protokol != "https"){
        echo "Xatolik, So'rov HTTPS protokolida bo'lishi shart !<br> SSl sertifekat kerak domainga !";
    }else{
        echo $webhook_url = "https://api.telegram.org/bot".API_KEY."/setWebHook?url=".$protokol."://".$_SERVER['HTTP_HOST']."".$_SERVER['SCRIPT_NAME'];
    };
    dump(reformat(file_get_contents($webhook_url)));
};

$update = json_decode(file_get_contents('php://input'));

// message variables
$message = $update->message;
$text = html($message->text);
$chat_id = $message->chat->id;
$chat_type = $message->chat->type;
$from_id = $message->from->id;
$message_id = $message->message_id;
$first_name = $message->from->first_name;
$last_name = $message->from->last_name;
$full_name = html($first_name . " " . $last_name);

// call back
$call = $update->callback_query;
$call_from_id = $call->from->id;
$call_id = $call->id;
$call_data = $call->data;
$call_message_id = $call->message->message_id;

if($chat_type == "private"){

    if($text == "/start"){
        $hi_text = "Assalom alaykum, <b>".$company_name."</b>ga hush kelibsiz.\n<code>Instagramdan video va suratlarni bepul yuklash uchun bizning kamallarga obuna bo'lishingiz shart !</code> 👇";
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => $hi_text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => get_chans()
            ])
        ]);
    }else if(mb_stripos($text, "/p/") !== false){
        $data = get_data($api_url.''.$text)['result'];
        if($data['__typename'] == "GraphVideo"){
            bot('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $data['display_resources'][2]['src'],
                'caption' => "Marhamat video tayyor 👇",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text'=> "Yuklash 📥", 'url' => $data['video_url']]]
                    ]
                ])
            ]);
        }else if($data['__typename'] == "GraphImage"){
                bot('sendPhoto', [
                    'chat_id' => $chat_id,
                    'photo' => $data['display_url'],
                    'parse_mode' => 'HTML',
                ]);
        }else if($data['__typename'] == "GraphSidecar"){
                $medea = [];
                    foreach ($data['edge_sidecar_to_children']['edges'] as $el){
                        if($el['node']['__typename'] == "GraphImage"){
                            $medea[] = ['type' => 'photo', 'media' => $el['node']['display_url']];
                        };
                    };
            if(count($medea) > 1){
                bot('sendMediaGroup', [
                    'chat_id' => $chat_id,
                    'media' => json_encode($medea)
                ]);
            }else{
                bot('sendPhoto', [
                    'chat_id' => $chat_id,
                    'photo' => $data['display_url'],
                    'parse_mode' => 'HTML',
                ]);
            };
        };
    }else if($text){
        if(user_is_followed($chat_id)){
            $reply = "Instagram tarmog'idan istalgan post havolasini yuboring:\n <code>https://www.instagram.com/p/CO2HmHOA09V/</code>";
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => $reply,
                'parse_mode' => 'HTML'            
            ]);
        }else{
            $hi_text = "<code>Xatolik, Instagramdan video va suratlarni bepul yuklash uchun bizning kamallarga obuna bo'lishingiz shart !</code> 👇";
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => $hi_text,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => get_chans()
                ])
            ]);
        };
    };
};
if($call_data == "followed"){
    if(user_is_followed($call_from_id)){
        $reply = "Obuna tasdiqlandi, Instagram tarmog'idan istalgan post havolasini yuboring:\n <code>https://www.instagram.com/p/CO2HmHOA09V/</code>";
        bot('editMessageText', [
            'chat_id' => $call_from_id,
            'message_id' => $call_message_id,
            'text' => $reply,
            'parse_mode' => 'HTML'            
        ]);
    }else{
        $reply = "Xatolik, Botdan foydalanish uchun barcha kanallarga obuna bo'lish shart !";
        bot('answerCallbackQuery', [
            'callback_query_id' => $call_id,
            'text' => $reply,
            'show_alert' => false            
        ]);
    };
};

// 'inline_keyboard' => [
//     [
//         ['text' => "test public", 'url' => "https://t.me/najot_uz_rasmiy"]
//     ]
// ]
