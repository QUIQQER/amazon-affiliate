<?php

namespace QUI\AmazonAffiliate;

/**
 * Class LinkParser
 *
 * @package QUI\AmazonAffiliate
 *
 * @author  www.pcsg.de (Jan Wennrich)
 * @licence For copyright and license information, please view the /LICENSE.md
 *
 */
class LinkParser
{
    /**
     * Define supported domains here
     */
    const DOMAINS = array('at', 'com', 'de', 'co.uk');

    /**
     * Appends Amazon affiliate tags to Amazon URLs in a string
     *
     * @param string $string
     *
     * @return string
     */
    public static function parse($string)
    {
        $Config = \QUI::getPackage('quiqqer/amazon-affiliate')->getConfig();

        // Turn domains into regex OR expression (e.g. "de|at|com|co.uk"
        $domainsRegex = implode('|', self::DOMAINS);

        // Regex gets for all Amazon URLs and their paths under the above defined domains
        $string = preg_replace_callback(
            "/http(?:s)?:\/\/(?:www\.)?amazon\.($domainsRegex)\/[\d\w-\._~:\/\?#\[\]@!$&'\(\)\*+,;=`]*/i",
            function ($matches) use ($Config) {
                // $matches[0] contains the whole URL and $matches[1] contains the TLD (de, com, etc.)

                $url = $matches[0];

                // If the URL already has a ?-parameter append a &-parameter, otherwise make it a ?-parameter
                if (!strpos($url, '?')) {
                    $tag = '?';
                } else {
                    $tag = '&';
                }

                // Replace dots in TLD with underscores (e.g. for co.uk) so we can access config value
                $tld = str_replace('.', '_', $matches[1]);

                // Get the tag for the TLD from the config
                $tag .= 'tag=' . $Config->getValue('tags', $tld);

                // Append the tag to the URL
                $url = $url . $tag;

                // Final URL which preg_replace uses to replace the found URL
                return $url;
            },
            $string
        );

        return $string;
    }
}
