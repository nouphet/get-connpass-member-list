<?php

require_once("./phpQuery-onefile.php");
require_once("./get-all-event-url-list.php");

/**
 * 配列から空要素を削除
 * @param $var
 * @return null|string
 */
function valuecomp($var)
{
    // null以外の値を返す
    $var = trim($var);
    if ($var <> null) {
        return trim($var);
    } else {
        return null;
    }
}

/**
 * Twitter URLを取得
 * @param $dom
 * @param $key
 * @return array|null|phpQueryObject|string
 */
function getTwitter($dom, $key) {
    $key = $key + 1;
    $tw = $dom["#main > div.applicant_area > div:nth-child(3) > table > tbody > tr:nth-child($key) > td.social.text_center"]->find("a");
    $a = pq($tw);
    $attr = $a->attr("href");
//    echo $attr;
    return $attr;
}

/**
 * GitHub URLを取得
 * @param $dom
 * @param $key
 * @return array|null|phpQueryObject|string
 */
function getGitHub($dom, $key) {
    $key = $key + 1;
    $gh = $dom["#main > div.applicant_area > div:nth-child(3) > table > tbody > tr:nth-child($key) > td.social.text_center > a.last"];
    $a = pq($gh);
    $attr = $a->attr("href");
//    echo $attr;
    return $attr;
}

/**
 * メイン処理 データを取得し配列にする
 * @param $list
 * @param $dom
 * @return array
 */
function get_list($list, $dom, $times) {
    $data = [];
    $user_names = array_map('trim', explode("\n", $list));
    $user_names = array_filter($user_names, 'valuecomp');
    $user_names = array_merge($user_names);
    foreach($user_names as $key => $name) {
        $data[$key]['times'] = $times;
        $data[$key]['name'] = $name;
        $data[$key]['twitter'] = getTwitter($dom, $key);
        $data[$key]['github'] = getGitHub($dom, $key);
    }
    return $data;
}

function get_how_many_times($dom) {
    $times = $dom["#main > div.title_with_thumb.mb_20 > a"]->text();
    $times = str_replace('CakePHP3', '', $times); // 3が残ってしまうため前処理して消す
    $times = preg_replace('/[^0-9]/', '', $times);
    return $times;
}

/* main */
foreach($urls as $key => $url) {

    // 処理対象日のHTMLを取得
    $site = $url;
    $site = $site . 'participation/#participants';
    $html = file_get_contents($site);

    /** 取得したい日のHTMLを取得 @var TYPE_NAME $dom */
    $dom = phpQuery::newDocument($html);

    /** 参加者リストを取得 @var string $list */
    $list = $dom["#main > div.applicant_area > div:nth-child(3)"]->find(".display_name")->text();

    foreach(get_list($list, $dom, get_how_many_times($dom)) as $values) {
        fputcsv(STDOUT, $values);
    }

}
