<?php

require_once("./phpQuery-onefile.php");

// 取得したいwebサイトを読み込み
$site=$argv[1];
$site =  $site . 'participation/#participants';
$html = file_get_contents("$site");


// 取得したい情報を記述
$doc = phpQuery::newDocument($html);

function get_list($list) {
    $lines = explode("\n", $list);
    foreach($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            echo '* ' . $line . "\n";
        }
    }
    echo "\n";
}

// タイトル
echo $doc["head"]["title"]->text() . "\n";

// 現地参加者リスト
echo '## ' . $doc[".applicant_area > div:nth-child(3) > table > thead > tr > th > span.label_ptype_name"]->text();
echo "\n";
$list = $doc["#main > div.applicant_area > div:nth-child(3)"]->find(".display_name")->text();
get_list($list);

// リモート参加者リスト
echo '## ' . $doc["#main > div.applicant_area > div:nth-child(4) > table > thead > tr > th > span.label_ptype_name"]->text();
echo "\n";
$list = $doc["#main > div.applicant_area > div:nth-child(4)"]->find(".display_name")->text();
get_list($list);

// YYPHP主催者・スタッフ枠
echo '## ' . $doc["#main > div.applicant_area > div:nth-child(5) > table > thead > tr > th > span.label_ptype_name"]->text();
echo "\n";

$list = $doc["#main > div.applicant_area > div:nth-child(5)"]->find(".display_name")->text();
get_list($list);

