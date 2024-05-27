## リリース手順
### ローカルでリリーステスト
```
# インスタンスのスナップショット

# メンテナンスモード
$ php artisan down --secret="キー"
* http:://xxxx/シークレットキー でアクセス可能

# 本番DB エクスポート
$ DATE=`date '+%Y-%m-%d_%H_%M_%S'`
$ mysqldump -h ホスト名 -u root -proot ksring  > "/home/ユーザー名/backup_db/${DATE}_export_ksring.sql" --set-gtid-purged=OFF --verbose

# バックアップしたものをローカルにコピー (ローカルから打つ)
scp -i ~/.ssh/sshキー ユーザ名@IPアドレス:コピー元パス コピー先パス(ローカル)

# ローカルをmainブランチにする
$ git checkout main
$ git pull origin main

# パッケージアップデート
$ sudo yum update -y
$ composer update
$ npm update
$ npm run prod

# ローカルにDBをインポート 手順
$ mysql -h ホスト名 -u root -p
$ drop database ksring;
$ create database ksring character set utf8 COLLATE utf8_general_ci;
$ use ksring
$ source ~/backup_db/20xx-xx-xx_xx_xx_xx_export_ksring.sql

# .envをlocalに編集

# ユーザーのパスワードを全て 'test1234' にする
$ php artisan command:user_test_password

# .envをproductionに編集

# ↑ここまでが本番データと同じ状態を作る作業
# ↓デプロイ作業


# ローカルに開発ブランチに変更
$ git checkout 開発ブランチ

# パッケージアップデート
$ composer update
$ npm update
$ npm run prod

# マイグレーション状態確認
php artisan migrate:status

# マイグレーション && シーディング
$ php artisan migrate
$ php artisan db:seed

# キューの変更の反映(変更していなければスキップ)
php artisan queue:restart

# キャッシュ削除
sudo php artisan optimize:clear

# --デプロイ後 確認事項--
# cron
crontab -l
systemctl status crond.service
php artisan schedule:list

# Redis
systemctl status redis
redis-cli ping

# .envモードが本番(production)になっているか
php artisan tinker
echo config("app.env");

#apache 起動確認
systemctl status httpd.service
# 場合によってapache再起動
sudo systemctl restart httpd.service

#メンテナンスモード解除
php artisan up
```

### STGでもローカルで行ったリリーステストと同じことを行う。


### 本番リリース
```
# インスタンスのスナップショット

# 本番DB エクスポート
$ DATE=`date '+%Y-%m-%d_%H_%M_%S'`
$ mysqldump -h ホスト名 -u root -proot ksring  > "/home/ユーザー名/backup_db/${DATE}_export_ksring.sql" --set-gtid-purged=OFF --verbose

# メンテナンスモード
$ php artisan down --secret="キー"
* http:://xxxx/シークレットキー でアクセス可能

開発ブランチに変更
$ git checkout 開発ブランチ

# パッケージアップデート
composer update && npm update && npm run prod

# マイグレーション状態確認
php artisan migrate:status

# マイグレーション && シーディング(取扱注意! シーダーは元データが消えないようにすること!)
$ php artisan migrate
$ php artisan db:seed

# キューの変更の反映(変更していなければスキップ)
php artisan queue:restart

# キャッシュ削除
sudo php artisan optimize:clear

# 動作確認

# 開発ブランチにをmainブランチにマージ
# mainブランチに変更して反映
$ git checkout main
$ git pull origin main

# --デプロイ後 確認事項--
# cron
crontab -l
systemctl status crond.service
php artisan schedule:list

# Redis
systemctl status redis
redis-cli ping

# .envモードが本番(production)になっているか
php artisan tinker
echo config("app.env");

#apache 起動確認
systemctl status httpd.service
# 場合によってapache再起動
sudo systemctl restart httpd.service

メンテナンスモード解除
php artisan up
```