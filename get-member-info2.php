<?php

require_once './phpQuery-onefile.php';
require_once './get-all-event-url-list.php';

/**
 * 配列から空要素を削除
 * @param $var
 * @return null|string
 */
function valueComp($var) { // ここの戻り値の型宣言はどうやればいいのか？string指定するとnull返すなって怒られる
    // null以外の値を返す
    $var = trim($var);
    if ($var <> null) {
        return trim($var);
    } else {
        return null;
    }
}

function sleepRand() {
    sleep(mt_rand(1,3));
}

/**
 * Twitter URLを取得
 * @param $dom
 * @param $key
 * @return array|null|phpQueryObject|string
 */
function getTwitter($dom, $key): string {
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
function getGitHub($dom, $key): string {
    $key = $key + 1;
    $gh = $dom["#main > div.applicant_area > div:nth-child(3) > table > tbody > tr:nth-child($key) > td.social.text_center > a.last"];
    $a = pq($gh);
    $attr = $a->attr("href");
    return $attr;
}

//function getGithubeEmail($dom, $key): string {
//    $key = $key + 1;
//    $gh = $dom["#js-pjax-container > div > div.h-card.col-3.float-left.pr-3 > ul > li:nth-child(3) > a"];
//    $a = pq($gh);
//    $html = file_get_contents($a);
//    checkHtml($html);
//    $dom_gh = phpQuery::newDocument($html);
//    $email = $dom_gh("#js-pjax-container > div > div.h-card.col-3.float-left.pr-3 > ul > li:nth-child(3) > a");
//    return $email;
//}

/**
 * メイン処理 データを取得し配列にする
 * @param $list
 * @param $dom
 * @return array
 */
function printList($list, $dom, $times): array {
    $data = [];
    $user_names = array_map('trim', explode("\n", $list));
    $user_names = array_filter($user_names, 'valueComp');
    $user_names = array_merge($user_names);
    foreach($user_names as $key => $name) {
        $data[$key]['times'] = $times;
        $data[$key]['name'] = $name;
        $data[$key]['twitter'] = getTwitter($dom, $key);
        $data[$key]['github'] = getGitHub($dom, $key);
//        $data[$key]['github_email'] = getGithubeEmail($dom, $key);
    }
    return $data;
}

// 何回目の回か回数を取得する
function getHowManyTimes($dom): int {
    $times = $dom["#main > div.title_with_thumb.mb_20 > a"]->text();
    $times = str_replace('CakePHP3', '', $times); // 3が残ってしまうため前処理して消す
    $times = preg_replace('/[^0-9]/', '', $times);
    return $times;
}

/* HTMLが読み込めたかどうかをチェック */
function checkHtml($html): string {
    if ($html === false) {
        echo '$html is empty. Please chech your URL.' .  "\n";
        exit(1);
    }
    return true;
}

/* main */
foreach($urls as $key => $url) {

    // 処理対象日のHTMLを取得
    $url = $url . 'participation/';
    $html = file_get_contents($url);
    checkHtml($html);

    /** 取得したい日のHTMLを取得 @var TYPE_NAME $dom */
    $dom = phpQuery::newDocument($html);

    /** 参加者リストを取得 @var string $list */
    $list = $dom["#main > div.applicant_area > div:nth-child(3)"]->find(".display_name")->text();

    //
    foreach(printList($list, $dom, getHowManyTimes($dom)) as $values) {
        fputcsv(STDOUT, $values);
    }

    sleepRand();
}
