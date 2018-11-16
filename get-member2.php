<?php

ini_set('display_errors', "Off");
require_once './phpQuery-onefile.php';

class HtmlPreparation
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string|bool
     */
    private $html;

    /**
     * MemberList constructor.
     * @param $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * 取得したいwebサイトを読み込み
     */
    public function fetch(): void
    {
        $url = $this->url . 'participation/';
        $this->html = file_get_contents($url);
    }

    public function isInvalid(): bool
    {
        return $this->html === false;
    }

    public function export(): phpQueryObject
    {
        return phpQuery::newDocument($this->html);
    }
}

class MemberList
{
    /**
     * @var phpQueryObject
     */
    private $doc;

    public function __construct(phpQueryObject $doc)
    {
        $this->doc = $doc;
    }

    /**
     * 参加者のリストを取得して表示
     */
    public function printMembersOfParticipants(): void
    {
        for ($i = 3; $i <= 5; $i++) {
            echo '## ' . $this->doc["#main > .applicant_area > div:nth-child($i) > table > thead > tr > th > span.label_ptype_name"]->text();
            echo "\n";
            $list = $this->doc["#main > div.applicant_area > div:nth-child($i)"]->find(".display_name")->text();
            $this->printList($list);
        }
    }

    private function printList($list): void
    {
        $lines = explode("\n", $list);
        foreach($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                echo '* ' . $line . "\n";
            }
        }
        echo "\n";
    }
}

class Arguments
{
    /**
     * @var array
     */
    private $argv;

    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    public function isInvalid(): bool
    {
        return $this->argv[1] === null;
    }

    public function convert2String(): string
    {
        return $this->argv[1];
    }
}

class Application
{
    /**
     * @var Arguments
     */
    private $args;

    public function __construct(Arguments $args)
    {
        $this->args = $args;
    }

    public function run(): void
    {
        if ($this->args->isInvalid()) {
            echo 'Missing $url Parameter.' . "\n";
            echo 'Please set Connpass URL.' . "\n";
            echo 'ex.) php get-member.php https://yyphp.connpass.com/event/103258/' . "\n";
            exit(1);
        }

        $url = $this->args->convert2String();
        $htmlDoc = new HtmlPreparation($url);
        $htmlDoc->fetch();

        if ($htmlDoc->isInvalid()) {
            echo '$html is empty. Please check your URL.' .  "\n";
            exit(1);
        }

        $phpQueryObject = $htmlDoc->export();

        echo '# ' . $phpQueryObject["head"]["title"]->text() . "\n";
        echo  '①PHP歴、②話したいこと/聞きたいこと、③簡単な自己紹介（オプション）' . "\n";

        /* main */
        $memberList = new MemberList($phpQueryObject);
        $memberList->printMembersOfParticipants();
    }
}

$app = new Application(new Arguments($argv));
$app->run();

/** TODO
 * echoは別にする
 * phpQueryObjectに依存しまくっている
 * Markdownの構造とデータを別にする
 *
 * 構造をとってくる
 * 変換する
 * 出力する
 */
