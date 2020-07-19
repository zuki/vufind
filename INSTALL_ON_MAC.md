# VuFind 6.1.1をMacにインストール

```
Mac mini (Late 2014)
macOS Mojave (v.10.14.6)
```

## ソフトウェアパッケージのインストール

### OpenJDK以外

```bash
# Mac標準のApacheの停止
$ sudo apachectl stop   # Foregraund
$ sudo launchctl unload -w /System/Library/LaunchDaemons/org.apache.httpd.plist # Background

$ brew -v
Homebrew 2.2.6
Homebrew/homebrew-core (git revision 04925; last commit 2020-02-19)
Homebrew/homebrew-cask (git revision 35bc11; last commit 2020-02-20)
$ brew install httpd
$ httpd -version
Server version: Apache/2.4.41 (Unix)
$ brew install php
$ php -version
PHP 7.4.2 (cli) (built: Jan 22 2020 06:34:34) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.2, Copyright (c), by Zend Technologies
$ brew install mysql
$ mysql --version
mysql  Ver 8.0.19 for osx10.14 on x86_64 (Homebrew)
```

### OpenJDK

インストール済みだった。たしか、この手順で導入したはず。

```bash
$ wget https://download.java.net/java/GA/jdk13.0.1/cec27d702aa74d5a8630c65ae61e4305/9/GPL/openjdk-13.0.1_osx-x64_bin.tar.gz
$ tar zxf openjdk-13.0.1_osx-x64_bin.tar.gz
$ sudo mv jdk-11.jdk /Library/Java/JavaVirtualMachines/
$ /usr/libexec/java_home  -V
Matching Java Virtual Machines (3):
    13, x86_64:	"OpenJDK 13"	/Library/Java/JavaVirtualMachines/openjdk-13.jdk/Contents/Home
    1.8.0_25, x86_64:	"Java SE 8"	/Library/Java/JavaVirtualMachines/jdk1.8.0_25.jdk/Contents/Home
    1.7.0_75, x86_64:	"Java SE 7"	/Library/Java/JavaVirtualMachines/jdk1.7.0_75.jdk/Contents/Home
$ export JAVA_HOME=`/usr/libexec/java_home -v 13`
$ java -version
openjdk version "13" 2019-09-17
OpenJDK Runtime Environment (build 13+33)
OpenJDK 64-Bit Server VM (build 13+33, mixed mode, sharing)
```

## VuFindのインストール

[VuFind on Ubuntu](https://vufind.org/wiki/installation:ubuntu)の`6. Install VuFind`に基づいてインストール。当初、Java SE 8を使用していたがSolrが起動できないため、上のようにOpenJDK 13を使うようにした。それ以外、特に問題になることはなかった。

```bash
$ wget https://github.com/vufind-org/vufind/releases/download/v6.1.1/vufind-6.1.1.tar.gz
$ tar xvf vufind-6.1.1.tar.gz
$ mv vufind-6.1.1 /usr/local/vufind
$ cd /usr/local/vufind
$ php install.php       # 注1
$ vi /usr/local/etc/httpd/httpd.conf
  User user
  Group staff
  LoadModule php7_module /usr/local/opt/php/lib/httpd/modules/libphp7.so
  <FilesMatch \.php$>
      SetHandler application/x-httpd-php
  </FilesMatch>
  Include /usr/local/etc/httpd/other/*.conf
$ mkdir -p /usr/local/etc/httpd/other
$ ln -s /usr/local/vufind/local/httpd-vufind.conf /usr/local/etc/httpd/other/vufind.conf
$ brew services restart httpd
$ mkdir -p /usr/local/vufind/local
$ export VUFIND_HOME=/usr/local/vufind
$ export VUFIND_LOCAL_DIR=/usr/local/vufind/local

$ cd /usr/local/vufind
$ ./solr.sh start   # 注2
$ http://localhost/vufind/Install/Home # 注3
```

- [注1] homebrew版httpdはユーザ権限で動くのでchownは不要
- [注2] [Limit Warnings](https://vufind.org/wiki/administration:starting_and_stopping_solr)が出たがひとまず無視
- [注3] すべてOKになるまで調整・変更。

## VuFind 2.1用の変更・独自開発分をVuFind 6.1.1に適用

### データのインポート

https://github.com/zuki/vufind/commit/379d69157ab8c2263ba3c69b3be5733ec59a8090

- solr/vufind/biblio/config/schema.xmlの編集
  - 日本語検索のためのフィールド型: text_ja の作成とフィールドへの適用
  - 上記フィールド型の適用とVuFind v2.1とv6.1.1の違いを調整
  - text_ja用の作業ファイルはそのまま使用可
- フィールド定義
  - フィールドカスタマイズ関数をbshからjavaに書き直し
    - JPMARC, USMARC, オリジナル作成に基づくID番号の作成
    - ISBNの正規化（10,13桁両ISBNの作成）
    - ISSNの正規化
    - NDLSHの取得・加工
    - 配架記号に基づくコレクション名の作成
  - VuFind v2.1とv6.1.1の違いを調整
- JPMARC用translation_map
  - v2.1用に作成したファイルがそのまま使用可
- 配架記号を表示するための独自`ILS/Driver/MyLibrary`
  - ドライバをFactoryクラスで作成するようになった
  - module/Zuki/src/Zuki/ILS/Driver/(MyLibrary|MyLibraryFactory).java

```bash
$ cd /usr/local/vufind
$ vi local/import/index_java/src/jp/zuki_ebetsu/index/JPMarcTools.java
$ vi local/import/jpmarc.properties  # JPMARC固有のフィールド定義
$ vi local/import/import.properties  # JPMARCインポート用
solr.indexer.properties = marc.properties, jpmarc.properties
./import-marc -p local/import/import.properties ../data/marc/jp-marc.mrc    # JPMARCのインポート
$ vi local/import/usmarc.properties  # 個人目録独自のUSMARC用フィールド定義
$ vi local/import/import_usmarc.properties  # USMARCインポート用
solr.indexer.properties = marc.properties, usmarc.properties
./import-marc -p local/import/import_usmarc.properties ../data/marc/us-marc.mrc    # USMARCのインポート
```

### レコード登録機能

https://github.com/zuki/vufind/commit/ee1dc56c04739493744b135b37f112f7d4cb2c14

`Admin/Home`にレコード登録機能を追加した。これはISBNなどを使い、MARCデータを取得し、配架記号などの所蔵データを追加してVuFindに登録する機能である。

MARCデータの取得については、JPMARCは次の2工程

1. 国立国会図書館サーチAPIを利用してISBNなどから全国書誌番号を取得
2. この全国書誌番号でNDL-Bibを引き、JPMARCを取得

USMARCは次の1工程

1. LC SRU Serverを利用してISBNから直接、USMARCを取得

で取得し、さらに以下の手順でレコードを登録する

3. 配架記号や巻号などの所蔵データを入力
4. 取得したJPMARCに所蔵データをマージ
5. importスクリプトを実行してレコードを登録

VuFind 6.1.1への移行については

- 本体部分はほぼほぼそのまま使用できた。
- module.config.phpの作成には[ジェネレータ](https://vufind.org/wiki/development:code_generators)を使用した。
- Gearmanによるジョブキュー方式をやめ、VuFind内で完結するよう仕様を変更し、workerの処理をVuFindに組み込んだ。
- workerでのデータ取得にはHTTP_Clientで簡易的に行っていたが、PHP7で使用できるHTTP_Request2に変更し、やはり簡易方式（Backendクラスを作成しないという意味）で実現した。

