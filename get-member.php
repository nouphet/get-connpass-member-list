<?php

require_once './phpQuery-onefile.php';

function print_list($list) {
    $lines = explode("\n", $list);
    foreach($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            echo '* ' . $line . "\n";
        }
    }
    echo "\n";
}

// 取得したいwebサイトを読み込み
if ($argv[1] == null) {
    echo 'Missing $url Parameter.' . "\n";
    echo 'Please set Connpass URL.' . "\n";
    echo 'ex) php get-member.php https://yyphp.connpass.com/event/103258/' . "\n";
    exit;
}

$url = $argv[1];
$url = $url . 'participation/';
$html = file_get_contents($url);

if ($html == false) {
    echo '$html is empty. Please chech your url.' .  "\n";
    exit;
}

// 取得したい情報を記述
$doc = phpQuery::newDocument($html);

// タイトル
echo '# ' . $doc["head"]["title"]->text() . "\n";

// 現地参加者リスト
echo '## ' . $doc["#main > .applicant_area > div:nth-child(3) > table > thead > tr > th > span.label_ptype_name"]->text();
echo "\n";
$list = $doc["#main > div.applicant_area > div:nth-child(3)"]->find(".display_name")->text();
print_list($list);

// リモート参加者リスト
echo '## ' . $doc["#main > div.applicant_area > div:nth-child(4) > table > thead > tr > th > span.label_ptype_name"]->text();
echo "\n";
$list = $doc["#main > div.applicant_area > div:nth-child(4)"]->find(".display_name")->text();
print_list($list);

// YYPHP主催者・スタッフ枠
echo '## ' . $doc["#main > div.applicant_area > div:nth-child(5) > table > thead > tr > th > span.label_ptype_name"]->text();
echo "\n";

$list = $doc["#main > div.applicant_area > div:nth-child(5)"]->find(".display_name")->text();
print_list($list);

