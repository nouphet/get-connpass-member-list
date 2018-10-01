# get-connpass-member-list

## About

Connpassから参加者リストを一撃で取得するスクリプトです。
「現地参加」や「リモート参加」は参加者枠の名前です。対象の勉強会に合わせてカスタムして使ってください。

## How to use.

```bash
$ php get-member.php <告知ページURL>
```

### 例

```bash
$ php get-member.php https://yyphp.connpass.com/event/100185/
```

### Result

```bash
# #YYPHP #53【PHPの情報交換・ワイワイ話そう・仲間作り・ゆるめ・にぎやかめ】 - 参加者・申込者一覧 - connpass
## 現地参加
* menber_1
* menber_2
* menber_3

## リモート参加
* menber_4
* menber_5
* menber_6

## YYPHP主催者・スタッフ枠
* suin|｡･ω･)。o (<b>PHPer集まれ</b>)
* nouphet|｡･ω･)。o (PHPer初心者集まれ)
* reoring
```
