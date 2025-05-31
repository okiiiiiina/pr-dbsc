# 起動

```
php -S 127.0.0.1:8101 src/index.php
```

# そのほかメモ

### curl

**TLS 無**

```
curl http://127.0.0.1:8101/health
curl http://127.0.0.1:8101/api/auth/google-sso

curl http://localhost:8101/health
curl http://localhost:8101/api/auth/google-sso
```

**TSL 有**

```
curl https://localhost:8102/api/health
```

**ボツ系**
php -S localhost:8101 src/index.php
php -S localhost:8101 -t src/
php -S localhost:8101 src/index.php

localhost.pem # 公開キー（証明書）
localhost-key.pem # 秘密キー

tail -n 20 /opt/homebrew/var/log/nginx/error.log
