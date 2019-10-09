<?php

namespace QUI\AmazonAffiliate;

use QUI\Utils\StringHelper;

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
    const DOMAINS = ['at', 'com', 'de', 'co.uk'];

    /**
     * Appends Amazon affiliate tags to Amazon URLs in a string
     *
     * @param string $string
     *
     * @return string
     */
    public static function parse($string)
    {
        // Turn domains into regex OR expression (e.g. "de|at|com|co.uk"
        $domainsRegex = str_replace('.', '\.', implode('|', self::DOMAINS));

        // Regex gets for all Amazon URLs and their paths under the above defined domains
        $string = preg_replace_callback(
            "/http(?:s)?:\/\/(?:www\.)?amazon\.($domainsRegex)\/[\d\w\-\._~:\/\?#\[\]@!$&'\(\)\*+,;=`%]*/i",
            function ($matches) {
                // $matches[0] contains the whole URL and $matches[1] contains the TLD (de, com, etc.)

                $url = $matches[0];

                $urlParts = parse_url($url);

                // Couldn't parse URL or couldn't find a host -> don't touch URL
                if (!$urlParts || !isset($urlParts['host'])) {
                    return $url;
                }

                // If no query exists set it to blank (needed later)
                if (!isset($urlParts['query'])) {
                    $urlParts['query'] = '';
                }

                // Get the TLD from the host and store it in $tld
                preg_match('/^.*amazon\.(.*)$/', $urlParts['host'], $tld);

                // Couldn't get the TLD -> don't touch the URL
                if (!isset($tld[1])) {
                    return $url;
                }

                $tld = strtolower($tld[1]);

                // We don't care about this TLD -> don't touch URL
                if (!in_array($tld, self::DOMAINS)) {
                    return $url;
                }

                // Get the query parameters
                // We have to append a '?' here, because parse_url removes it but getUrlAttributes expects it
                $query = StringHelper::getUrlAttributes('?' . $urlParts['query']);

                // Set the proper tag
                $query['tag'] = self::getTagForTld($tld);

                // Rebuild the query to a string
                $urlParts['query'] = http_build_query($query);

                // Turn the URL-parts back to a string, which preg_replace uses to replace the found URL
                return StringHelper::unparseUrl($urlParts);
            },
            $string
        );

        $string = preg_replace_callback(
            "/(?<preview><div data-oembed-url=\"(?<url>http(?:s)?:\/\/(?:www\.)?amazon\.(?<domain>de|com|at|co\.uk)\/[\d\w\-\._~:\/\?#\[\]@!$&'\(\)\*+,;=`%]*)\">.*<\/script>.*<\/div>)/isU",
            function ($matches) {
                // $matches['url'] contains the whole URL and $matches['domain'] contains the TLD (de, com, etc.)

                $url     = $matches['url'];
                $preview = $matches['preview'];

                $replacement = "<a href=\"$url\">$preview</a>";

                return $replacement;
            },
            $string
        );

        return $string;
    }

    /**
     * Returns the Amazon-Affiliate-Tag set in the config for a given Top Level Domain (TLD)
     *
     * @param $tld
     *
     * @return array|bool|string
     *
     * @throws \QUI\Exception
     */
    protected static function getTagForTld($tld)
    {
        $Config = \QUI::getPackage('quiqqer/amazon-affiliate')->getConfig();

        // Replace dots in TLD with underscores (e.g. for co.uk) so we can access config value
        $tld = str_replace('.', '_', $tld);

        // Get the tag for the TLD from the config
        return $Config->getValue('tags', $tld);
    }
}
