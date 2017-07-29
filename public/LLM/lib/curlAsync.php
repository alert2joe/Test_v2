<?php
namespace LLM\lib;
class curlAsync{
static function async_get_url($url_array, $wait_usec = 0)
{
    if (!is_array($url_array))
        return false;

    $wait_usec = intval($wait_usec);

    $data    = array();
    $handle  = array();
    $running = 0;

    $mh = curl_multi_init(); // multi curl handler

    $i = 0;
    foreach($url_array as $url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($ch, CURLOPT_MAXREDIRS, 7);

        curl_multi_add_handle($mh, $ch); 
        $handle[$i++] = $ch;
    }

    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    /* get data */
    foreach($handle as $i => $ch) {
        $content  = curl_multi_getcontent($ch);
        $data[$i] = (curl_errno($ch) == 0) ? $content : false;
    }

    /* remove handle*/
    foreach($handle as $ch) {
        curl_multi_remove_handle($mh, $ch);
    }

    curl_multi_close($mh);

    return $data;
}
}
