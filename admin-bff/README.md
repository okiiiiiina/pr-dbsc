curl http://localhost:8101/api/auth/google-sso
curl http://localhost:8101/health

php -S localhost:8101 -t src/
php -S localhost:8101 src/index.php
php -S 127.0.0.1:8101 -t src/

localhost.pem # 公開キー（証明書）
localhost-key.pem # 秘密キー

curl https://localhost:8102/api/health

tail -n 20 /opt/homebrew/var/log/nginx/error.log
