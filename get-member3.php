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
        for ($i = 3; $i <= 6; $i++) {
            $text = $this->doc["#main > .applicant_area > div:nth-child($i) > table > thead > tr > th > span.label_ptype_name"]->text();
            $doc = new Output($text);
            $doc->exportMdH2();

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
                $text = $line . "\n";
                $doc = new Output($text);
                $doc->exportMdList();
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

    /**
     * Arguments constructor.
     * @param array $argv
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    public function isInvalid(): bool
    {
        return $this->argv[1] === null;
    }

    public function convertArray2String(): string
    {
        return $this->argv[1];
    }
}

class Output
{
    /**
     * @var string
     */
    private $msg;

    /**
     * @param string $msg
     */
    public function __construct(string $msg)
    {
        $this->msg = $msg;
    }

    public function exportMdH1()
    {
        print '# ' . $this->msg;
    }

    public function exportMdH2()
    {
        print '## ' . $this->msg . "\n";
    }

    public function exportMdList()
    {
        print '* ' . $this->msg;
    }

    public function exportMdPlainText()
    {
        print $this->msg;
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
            $text = 'Missing $url Parameter.' . "\n";
            $text .= 'Please set Connpass URL.' . "\n";
            $text .= 'ex.) php get-member.php https://yyphp.connpass.com/event/103258/' . "\n";
            $doc = new Output($text);
            $doc->exportMdPlainText();

            exit(1);
        }

        $url = $this->args->convertArray2String();
        $htmlDoc = new HtmlPreparation($url);
        $htmlDoc->fetch();

        if ($htmlDoc->isInvalid()) {
            $text = '$html is empty. Please check your URL.' .  "\n";
            $doc = new Output($text);
            $doc->exportMdPlainText();

            exit(1);
        }

        $phpQueryObject = $htmlDoc->export();

        $text = $phpQueryObject["head"]["title"]->text() . "\n";
        $doc = new Output($text);
        $doc->exportMdH1();

        $text = '①PHP歴、②話したいこと/聞きたいこと、③簡単な自己紹介（オプション）' . "\n";
        $doc = new Output($text);
        $doc->exportMdPlainText();

        /* main */
        $memberList = new MemberList($phpQueryObject);
        $memberList->printMembersOfParticipants();
    }
}

$app = new Application(new Arguments($argv));
$app->run();

/** TODO
 * echoは別にする                   : 2018/11/18 done
 * Markdownの構造とデータを別にする    : 2018/11/18 done
 * 参加回数を取得する
 *
 * DIPを実装する
 *  依存逆転させる
 *  下位が上位に依存させる
 * alt + enter
 * $this->とself::の違いを調べる
 *
 * phpQueryObjectに依存しまくっている
 *  スクレイピングをphpQueryでなくてもスクレイピングできるようにする
 *      メイン処理以外でrequireする
 *      ファイルを分ける
 *  構造をとってくる
 *  変換する
 *  出力する
 */
