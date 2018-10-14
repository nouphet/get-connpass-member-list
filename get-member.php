<?php

ini_set('display_errors', "Off");
require_once './phpQuery-onefile.php';

function print_list($list): void  {
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
    echo 'ex.) php get-member.php https://yyphp.connpass.com/event/103258/' . "\n";
    exit(1);
}

/* HTMLが読み込めたかどうかをチェック */
function check_html($html): string {
    if ($html === false) {
        echo '$html is empty. Please check your URL.' .  "\n";
        exit(1);
    }
    return true;
}

// 参加車のリストを取得して表示
function print_members_of_participants($doc): void {
    for ($i = 3; $i <= 5; $i++) {
        echo '## ' . $doc["#main > .applicant_area > div:nth-child($i) > table > thead > tr > th > span.label_ptype_name"]->text();
        echo "\n";
        $list = $doc["#main > div.applicant_area > div:nth-child($i)"]->find(".display_name")->text();
        print_list($list);
    }
}

// 取得したいwebサイトを読み込み
$url = $argv[1];
$url = $url . 'participation/';
$html = file_get_contents($url);
check_html($html);

// 取得したい情報の全体を取得
$doc = phpQuery::newDocument($html);

// タイトル
echo '# ' . $doc["head"]["title"]->text() . "\n";
echo  '①PHP歴、②話したいこと/聞きたいこと、③簡単な自己紹介（オプション）' . "\n";

/* main */
print_members_of_participants($doc);
