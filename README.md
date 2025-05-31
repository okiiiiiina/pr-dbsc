# ローカル証明書発行方法

### 1. mkcert のインストール

```
brew install mkcert
```

### 2. CA（認証局）をローカルに作成＆システムに登録

```
mkcert -install
The local CA is already installed in the system trust store! 👍
The local CA is already installed in the Firefox trust store! 👍
```

### 3. HTTPS 証明書を作成

```
mkcert localhost

// 出力されるファイル：
localhost.pem         # サーバー証明書（公開鍵）
localhost-key.pem     # 秘密鍵
```

### 4. Next.js の設定

server.js

```

const fs = require('fs')
const path = require('path')
const { parse } = require('url')
const next = require('next')
const https = require('https')

const port = 3101
const dev = true
const app = next({ dev, dir: './src' })
const handle = app.getRequestHandler()

const httpsOptions = {
  key: fs.readFileSync(path.resolve(__dirname, '../certs/localhost-key.pem')),
  cert: fs.readFileSync(path.resolve(__dirname, '../certs/localhost.pem')),
}

app.prepare().then(() => {
  https.createServer(httpsOptions, (req, res) => {
    const parsedUrl = parse(req.url, true)
    handle(req, res, parsedUrl)
  }).listen(port, () => {
    console.log(`✅ HTTPS server ready at https://localhost:${port}`)
  })
})


```

### 5. php のリバースプロキシの設定

/opt/homebrew/etc/nginx/servers/dbsc.conf

```
# https://localhost:8102 で待ち受け → PHP (http://localhost:8101)へ転送
server {
    listen 8102 ssl;
    server_name localhost;

    ssl_certificate     /{絶対パス}/practice/pr-dbsc/certs/localhost.pem;
    ssl_certificate_key /{絶対パス}/practice/pr-dbsc/certs/localhost-key.pem;

    # HTTP/2 を使いたい場合は：  listen 8102 ssl http2;

    location /api/ {
        proxy_pass         http://127.0.0.1:8101$request_uri;
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-Proto https;
    }
}
```

変更したら以下コマンド

```
nginx -t
sudo nginx -s reload
```

### その他

**生成された CA 証明書の場所を確認**

```
mkcert -CAROOT
```
