# 起動

```
php -S 127.0.0.1:8101 src/index.php
 php -S 127.0.0.1:8101 src
```

# そのほかメモ

### curl

**TLS 無**

```
curl http://127.0.0.1:8101/api/health
curl http://127.0.0.1:8101/api/auth/google-sso

curl http://localhost:8101/api/health
curl http://localhost:8101/api/auth/google-sso
```

**ボツ系**
php -S localhost:8101 src/index.php
php -S localhost:8101 -t src/
php -S localhost:8101 src/index.php

localhost.pem # 公開キー（証明書）
localhost-key.pem # 秘密キー

tail -n 20 /opt/homebrew/var/log/nginx/error.log
