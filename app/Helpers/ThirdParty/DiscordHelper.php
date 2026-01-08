<?php

namespace App\Helpers\ThirdParty;

use App\Constants\DiscordConstant;
use App\Traits\GlobalTrait;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Class DiscordHelper
 *
 * @package	App\Helpers
 * 
 */
class DiscordHelper
{
    use GlobalTrait;

    protected $webhook_avatar, $webhook_id, $webhook_token;

    public function __construct()
    {
        // $this->webhook_avatar = DiscordConstant::;
        $this->webhook_id = config('app.discord_webhook_id');
        $this->webhook_token = config('app.discord_webhook_token');
    }

    public function debugError(Throwable $throwable = null, $customMessage = null, $lokasi = null)
    {
        if (empty($customMessage)) :
            $text = "====================\n";
            $text .= "File : " . $throwable->getFile() . "\n";
            $text .= "Line : " . $throwable->getLine() . "\n\n";
            $text .= "Endpoint : " . Request::url() . "\n";
            $text .= "Message : " . $throwable->getMessage() . "\n\n";
            // $text .= "Author : " . config("app.author") . " | " . Request::ip() . "\n";

            if ($lokasi != null) :
                $text .= "Lokasi : " . $lokasi . "\n";
            endif;

            $text .= date("d-m-Y H:i:s") . "\n";
            $text .= "====================\n";
        else :
            $text = $customMessage . "\n\n";

            if ($lokasi != null) :
                $text .= "Lokasi : " . $lokasi . "\n";
            endif;

            $text .= date("d-m-Y H:i:s") . "\n";
        endif;

        $webhook_url = "https://discord.com/api/webhooks/$this->webhook_id/$this->webhook_token";
        $this->sendTo($webhook_url, $text);
    }

    public function debugSuccess($customMessage = null, $data = null)
    {
        $text = "====================\n";
        $text .= date("d-m-Y H:i:s") . "\n";
        $text .= "====================\n";
        $text .= "Message : " . $customMessage . "\n\n";
        $text .= "====================\n";

        if ($data != null) :
            $text .= "Data : " . json_encode($data) . "\n\n";
        endif;

        $webhook_url = "https://discord.com/api/webhooks/$this->webhook_id/$this->webhook_token";
        $this->sendTo($webhook_url, $text);
    }

    private function sendTo($webhookUrl, $text)
    {
        $content = [
            "username"   => "Athar ",
            "avatar_url" => $this->webhook_avatar,
            "tts"        => false,
            "content"    => $text
        ];

        // send post with curl
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
