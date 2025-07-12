## 起動

```
cd admin-bff
php -S 127.0.0.1:8101 src/index.php
php -S 127.0.0.1:8101 -t src src/index.php
```

## test

### GET

```
※  helthcheck の middlware を off にしてから確認する「'middleware' => []」

curl http://localhost:8101/api/health
```

### POST

```
※  該当API の middlware を off にしてから確認する「'middleware' => []」

curl -X POST http://localhost:8101/api/workspaces \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhdXRoMHw2ODA1MTc1YzQzOTMyMzFmZWFlZGI3NzQiLCJleHAiOjE3NTIzMzA1MjAsImF1ZCI6Imx2Z3MtcHJhY3RpY2UtYXBwIn0.35S3KbZWQoiZmxJbPVKMHqrJJIm5o3kIZrZhscy52rU" \
  -d '{"name": "テスト", "plan": "pro"}'
```

## 認証・認可フロー（Auth0 + 自前 JWT）

### 🔐 ログインフロー

1. ユーザーが「ログイン」ボタンをクリック
2. バックエンドで Auth0 のドメイン・client_id を用いて SSO リンクを生成
3. クライアントに SSO リンクを返す
4. ユーザーが SSO リンクにアクセスし、Auth0 にログイン
5. 認証後、Auth0 がリダイレクト URL に `code` を付与してリダイレクト
6. クライアントが `code` をバックエンドに POST
7. バックエンドで Auth0 に `code` を送信して `token` を取得
   - `access_token`（形式: JWE）
   - `id_token`（形式: JWT）
8. `access_token` を使って Auth0 の `/userinfo` エンドポイントからユーザー情報を取得（初回のみ）
   - **取得したユーザー情報を DB に保存**
9. ユーザー情報から `sub` を取り、自前の JWT を作成して Cookie にセット
   - `sub`: ユーザー ID（Auth0 の一意識別子）
   - `exp`: 有効期限（例: 1 時間）
   - `aud`: 固定値（例: `"my-api"`、`.env` の `JWT_AUDIENCE` など）
10. JWT を `Secure: true, HttpOnly: true` の Cookie にセット

---

### 🛡 API リクエスト時の認証チェック

1. クライアントが API をリクエスト
2. バックエンドのミドルウェアで Cookie から JWT を取得し検証
   - `sub`: DB に同じユーザーが存在するか確認
   - `aud`: `.env` の `JWT_AUDIENCE` と一致するか確認
   - `exp`: 有効期限が切れていないか確認
3. DB 上で一致する `sub` のユーザーが見つかれば、`role` 情報も取得してリクエストに紐付け
4. その API を呼ぶのに必要な権限があるか、認可ミドルウェアで確認
5. 問題なければ処理を続行し、レスポンスを返す

---

### 🔧 補足ポイント

- `JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'))` のように署名アルゴリズムの明示が必要です
- 自前 JWT の `aud` は、**受け取り対象となる API を識別する固定値**が望ましく、Auth0 の `client_id` を使う必要はありません
- 認証（JWT 検証）と認可（ロール判定）は、**ミドルウェアを分けて管理**すると保守性が高まります
- `access_token` を使って `userinfo` を取得するのはログイン直後だけに限定し、その後は DB の `sub` を使って処理してください
