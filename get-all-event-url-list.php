<?php

require_once("./phpQuery-onefile.php");

$site = "https://yyphp.connpass.com/event/?page=1";
$html = file_get_contents("$site");
$dom = phpQuery::newDocument($html);

/**
 * 配列から空要素を削除
 * @param $var
 * @return null|string
 */
function value_num($var)
{
    // null以外の値を返す
    $var = trim($var);
    if ($var <> null) {
        if (ctype_digit($var)) {
            return trim($var);
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function value_comp($var)
{
    // null以外の値を返す
    $var = trim($var);
    if ($var <> null) {
        return trim($var);
    } else {
        return null;
    }
}

function get_pages($dom) {
    $arr_numbers = [];
    $number_paging = $dom["#contents > div:nth-child(3) > div.group_box > div > div.paging_area > ul > li:nth-child > a"];
    $a = pq($number_paging);
    $arr_numbers = explode("\n", $a->text());
    $arr_numbers = array_filter($arr_numbers, 'value_num');
    return max($arr_numbers);
}

function get_url_list($page_number) {
    for ($i=1; $i <= $page_number; $i++) {

        $site = "https://yyphp.connpass.com/event/?page=$i";
        $html = file_get_contents("$site");
        $dom = phpQuery::newDocument($html);

        for ($j=1; $j <=10; $j++) {

            $list = $dom["#contents > div:nth-child(3) > div.group_box > div > div:nth-child($j) > div.group_event_inner > p.event_title > a"];
            $a = pq($list);
            $attr = $a->attr("href");
            $url[] = $attr;
        }
    }
    return $url;
}

$page_number = get_pages($dom);
$urls = get_url_list($page_number);
$urls = array_filter($urls, 'value_comp');

return $urls;
