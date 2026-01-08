<?php

namespace App\Traits;

use App\Helpers\ThirdParty\DiscordHelper;
use Throwable;

trait GlobalTrait
{
    public function devResponse(Throwable $th = null, $lokasi = null, $customMessage = null)
    {
        // if (app()->bound('sentry')) {
        //     app('sentry')->captureException($th);
        // }

        $discord = new DiscordHelper();
        $discord->debugError(throwable: $th, lokasi: $lokasi, customMessage: $customMessage);
        return $th->getMessage() . " at line " . $th->getLine() . " in " . $th->getFile();
    }

    public function devResponseSuccess($message = 'success', $data = null)
    {
        $discord = new DiscordHelper();

        $discord->debugSuccess(customMessage: $message, data: $data);

        return $message;
    }
}
