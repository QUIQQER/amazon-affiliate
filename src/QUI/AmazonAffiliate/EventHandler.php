<?php
namespace QUI\AmazonAffiliate;

use QUI\System\Log;

class EventHandler
{
    public static function onSiteInit()
    {
        Log::write("banana");
    }

    public static function onRequestOutput()
    {
        Log::write("hallon");
    }

    public static function onAdminLoadFooter()
    {
        Log::write("TEST123");
    }
}
