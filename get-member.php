<?php

ini_set('display_errors', "Off");
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

// 引数があるかチェック
if ($argv[1] == null) {
    echo 'Missing $url Parameter.' . "\n";
    echo 'Please set Connpass URL.' . "\n";
    echo 'ex) php get-member.php https://yyphp.connpass.com/event/103258/' . "\n";
    exit;
}

// 取得したいwebサイトを読み込み
$url = $argv[1];
$url = $url . 'participation/';
$html = file_get_contents($url);

/* HTMLが読み込めたかどうかをチェック */
if ($html == false) {
    echo '$html is empty. Please chech your URL.' .  "\n";
    exit;
}

// 取得したい情報を記述
$doc = phpQuery::newDocument($html);

// タイトル
echo '# ' . $doc["head"]["title"]->text() . "\n";
echo  '①PHP歴、②話したいこと/聞きたいこと、③簡単な自己紹介（オプション）' . "\n";

function print_member_of_participants($doc) {
    for ($i = 3; $i <= 5; $i++) {
        echo '## ' . $doc["#main > .applicant_area > div:nth-child($i) > table > thead > tr > th > span.label_ptype_name"]->text();
        echo "\n";
        $list = $doc["#main > div.applicant_area > div:nth-child($i)"]->find(".display_name")->text();
        print_list($list);
    }
}

/* main */
print_member_of_participants($doc);
