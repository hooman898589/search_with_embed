<?php
require __DIR__ . '/vendor/autoload.php';
header("Access-Control-Allow-Origin: http://127.0.0.1:3000");

header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}
$client = new \GuzzleHttp\Client();

$body=json_decode(file_get_contents("php://input"));
$count=count($body->messages)-1;

if (!$body || !isset($body->messages) || count($body->messages)==0){
    http_response_code(400);
    echo json_encode(["error"=>"Invalid request"]);
    exit;
}
$request=$client->post("http://127.0.0.1:11434/api/embed",[
   'json'=>[
       "model"=> "nomic-embed-text:latest",
        "input"=> $body->messages[$count]->content
   ]
]);

$rbody=json_decode($request->getBody()->getContents());

$embed=$rbody->embeddings;



$request=$client->post('http://127.0.0.1:6333/collections/ai-chat/points/search',[
    "json"=>[
        "vector"=>$embed[0],
        "limit"=>3,
        "with_payload"=>true
]
    ]);

$messges="";
$rbody=json_decode($request->getBody()->getContents());
$messges.="This information is provided to help give better answers:\n\n";
foreach ($rbody->result as $result){
    $messges .= $result->payload->text . "\n\n";
}
$i=1;
foreach ($body->messages as $history){
    if ($i<=$count) {
        $messges .= "\n\n role : $history->role | content : $history->content \n";
    }
    $i+=1;
}
$messges.="this is main question of user and you must answer it : ".$body->messages[$count]->content;
$request=$client->post('http://127.0.0.1:11434/api/chat',[
   "json"=>[
       'model'=>"qwen2.5-coder:7b",
       "messages"=>[
           [
               "role"=>"system",
               "content"=>"تو یک دستیار فارسی‌زبان هستی. همیشه و بدون استثنا پاسخ نهایی خودت را به زبان فارسی بنویس، حتی اگر کاربر انگلیسی یا زبان دیگری صحبت کرد؛ مگر اینکه کاربر صراحتاً درخواست کند متن را به زبان دیگری تولید کنی. در مکالمات عادی کاملاً خودمانی، طبیعی و دوستانه صحبت کن و از لحن خشک و رسمی پرهیز کن. از اصطلاحات محاوره‌ای فارسی استفاده کن. اگر کاربر شوخی کرد، متناسب با همان فضا و به شکل طبیعی پاسخ بده. هیچ‌وقت اطلاعاتی را که نمی‌دانی از خودت نساز. فقط زمانی از ابزارها استفاده کن که درخواست کاربر واقعاً به آن ابزار مربوط باشد و برای اجرای ابزار هیچ‌وقت پارامترها را حدس نزن یا از خودت تولید نکن"
           ],
           [
                "role"=>"user",
                "content"=>$messges
               ]
       ],
       "stream"=>false
   ]
]);

$response=json_decode($request->getBody()->getContents());


echo json_encode(["message"=>$response->message->content]);