<?php

namespace App\Service;

use Bepsvpt\Blurhash\Facades\BlurHash;
use Exception;

class Service
{
    public function validate($validate)
    {
        return response()->json([
            'status' => 'failed',
            'message' => $validate->errors()->first(),
        ], 400);
    }

    public function notFound()
    {
        return response()->json([
            'status' => 'failed',
            'message' => 'Not Found',
        ], 404);
    }

    public function unauthorizedMessage(){
        return response()->json([
            'status' => 'failed',
            'message' => 'Unauthorized',
        ], 401);
    }

    public function errorMessage(\Exception $exception)
    {
        return $exception->getMessage();
        $this->sendMessageTelelegram($exception->getMessage());
        return response()->json([
            'status' => 'failed',
            'message' => 'Something went wrong',
        ], 500);
    }

    public function blurHashEncode($path)
    {
        return  BlurHash::encode($path);
    }

    public function sendMessageTelelegram($message)
    {
        $update = json_decode(file_get_contents("php://input"), true);
        if (isset($update["message"])) {
            $chat_id = $update["message"]["chat"]["id"];
            $text = $update["message"]["text"];

            // Process message
            $reply = "You said: " . $text;

            // Send response
            $this->sendMessage($chat_id, $reply, env('TELEGRAM_BOT_TOKEN'));
        }
    }

    // Function to send a message
    function sendMessage($chat_id, $message, $token)
    {
        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
    }
}
