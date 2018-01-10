<?php

namespace QUI\AmazonAffiliate;

class LinkParser
{
    public static function parse($string)
    {
        $de = array('xgf0rum-21', 'xgf0rum-21', 'xgf0rum-21', 'xgforum-21');
        $uk = array('nxuk-21', 'nxco-21');

        $programIDs = array(
            'co.uk' => $uk[array_rand($uk)],
            'com'   => 'nxcom-20',
            'de'    => $de[array_rand($de)],
            'at'    => $de[array_rand($de)],
        );

        $amazonDomains = "de|com|co.uk|at";

        if (strstr($string, "amazon.")) {
            $string = str_replace('http://amazon', 'https://www.amazon', $string);
            $string = str_replace('http://www.amazon', 'https://www.amazon', $string);

            preg_match("/^https:\/\/www\.amazon\.(" . $amazonDomains . ")\//i", $string, $matches);

            if (count($matches)) {
                $restlink = preg_replace("/^https:\/\/www\.amazon\.(" . $amazonDomains . ")\//i", '', $string);

                $restlink = html_entity_decode($restlink);
                $restlink = preg_replace("#(&tag=[\w-]+)#is", '', $restlink);
                $restlink = preg_replace("#(tag=[\w-]+)#is", '', $restlink);

                if (strpos($restlink, '?') !== false && strpos($restlink, '?') < 200) {
                    $programID = $programIDs[$matches[1]];
                    $tag       = preg_replace('/\?/', '?tag=' . $programID . '&', $restlink, 1);
                    $affili    = "https://www.amazon." . urlencode($matches [1]) . "/" . $tag;
                } else {
                    if (strpos($restlink, '?') === false) {
                        $programID = $programIDs[$matches[1]];
                        $tag       = preg_replace('/"/', '?tag=' . $programID . '"', $restlink, 1);
                        $affili    = "https://www.amazon." . urlencode($matches [1]) . "/" . $tag;
                    } else {
                        $affili = "https://www.amazon." . urlencode($matches [1]) . '/' . $restlink;
                    }
                }

                $strarr[$i] = $affili;
            }

            for ($i = 0; $i < count($strarr); $i++) {
                if (strpos($strarr[$i], 'amazon.') === false) {
                    continue;
                }

                $strarr[$i] = str_replace('http://amazon', 'https://www.amazon', $strarr[$i]);
                $strarr[$i] = str_replace('http://www.amazon', 'https://www.amazon', $strarr[$i]);

                preg_match("/^https:\/\/www\.amazon\.(" . $amazonDomains . ")\//i", $strarr [$i], $matches);

                if (count($matches)) {
                    $restlink = preg_replace("/^https:\/\/www\.amazon\.(" . $amazonDomains . ")\//i", '', $strarr [$i]);

                    $restlink = html_entity_decode($restlink);
                    $restlink = preg_replace("#(&tag=[\w-]+)#is", '', $restlink);
                    $restlink = preg_replace("#(tag=[\w-]+)#is", '', $restlink);

                    if (strpos($restlink, '?') !== false && strpos($restlink, '?') < 200) {
                        $affili = "https://www.amazon." . urlencode($matches [1]) . "/" . preg_replace('/\?/',
                                '?tag=' . $programIDs[$matches[1]] . '&', $restlink, 1);

                    } else {
                        if (strpos($restlink, '?') === false) {
                            $affili = "https://www.amazon." . urlencode($matches [1]) . "/" . preg_replace('/"/',
                                    '?tag=' . $programIDs[$matches[1]] . '"', $restlink, 1);
                        } else {

                            $affili = "https://www.amazon." . urlencode($matches [1]) . '/' . $restlink;
                        }
                    }

                    $strarr[$i] = $affili;
                }
            }

            $string = implode("[url]", $strarr);
        }

        return $string;
    }
}
