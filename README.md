# 勤怠管理アプリ

## 1. Dockerの設定

1. クローン

```
git clone git@github.com/Noriko30402/revision-worklog-app
 ```

2. dockerをビルド

```
docker-compose up -d --build
```

3. mac環境の場合『　docker-compose.yml　』ファイルの変更が必須<br>
   mysql:内にて下記を追記

  ```
    platform: linux/amd64
  ```

## 2. Laravel の環境構築

1. PHP dockerにてインストール

```
docker-compose exec php bash
composer install
 ```

2. env.exampleをコピーして .envファイル作成し環境変数を変更

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

3. phpを使用するためキーを作成 (PHPのdocker内)

```
php artisan key:generate
php artisan config:clear
```

4. マイグレーションの実行

```
php artisan migrate
```

5. シーディングの実行

```
php artisan db:seed
```

## 3.Fortyfy実装

```
composer require laravel/fortify
php artisan vendor:publish -provider="Laravel\Fortify\FortifyServiceProvider"
php artisan migrate
composer require laravel-lang/lang:~7.0 --dev
cp -r ./vendor/laravel-lang/lang/src/ja ./resources/lang/
```

## 5. メール認証

mailtrapというツールを使用しています。<br>
以下のリンクから会員登録をしてください。　

<https://mailtrap.io/>

メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、<br>
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。<br>
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。　

## 6. テストアカウント
###　スタッフ
名前: 一般ユーザ<br>
メールアドレス: general1@gmail.com　<br>
パスワード: password<br>

名前: 一般ユーザ<br>
メールアドレス: general2@gmail.com<br>
パスワード: password

##　管理者
名前：管理者1<br>
メールアドレス: admin-one@test.com<br>
パスワード: password<br>

名前：管理者2<br>
メールアドレス: admin-two@test.com<br>
パスワード: password<br>

名前：管理者3<br>
メールアドレス: admin-tree@test.com<br>
パスワード: password<br>


### PHPUnitを利用したテスト環境構築方法

```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database test_database;

docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit
```


## 5. 使用技術(実行環境)

・Docker. Ver 27.3.1<br>
・php:8.1<br>
・Laravel v10.48.25<br>
・Homebrew Server version: 9.0.1<br>
・mysql  Ver 8.0.26 for Linux on x86_64<br>
・nginx  1.21.1

## 6. 開発環境 URL

管理者画面ログインページ：<http://localhost/admin/login><br>
一般ユーザーログインページ：<http://localhost/staff/login><br>
phpMyAdmin: <http://localhost:8080>
