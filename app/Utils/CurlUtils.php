<?php
namespace App\Utils;
class CurlUtils
{
    public static function get($url)
    {
        //Get方式获取网页内容
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}