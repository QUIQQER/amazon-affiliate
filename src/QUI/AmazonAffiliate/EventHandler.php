<?php
namespace QUI\AmazonAffiliate;

use QUI\System\Log;

/**
 * Class EventHandler
 *
 * @package QUI\AmazonAffiliate
 *
 * @author  www.pcsg.de (Jan Wennrich)
 * @licence For copyright and license information, please view the /LICENSE.md
 *
 */
class EventHandler
{
    /**
     * Fired onRequestOutput
     *
     * @param $output
     */
    public static function onRequestOutput(&$output)
    {
        // Because we get the output variable as a reference we can simply replace it.
        // No need to check if we are in backend since this event is only fired for frontend pages.
        $output = LinkParser::parse($output);
    }
}
