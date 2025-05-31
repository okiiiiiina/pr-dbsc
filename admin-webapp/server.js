// adminwebapp/server.js
const fs = require('fs')
const https = require('https')
const { parse } = require('url')
const next = require('next')
const path = require('path')

const port = 3101
const dev = true
const app = next({ dev, dir: './src' })  // ← src 配下に app ディレクトリがある場合
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
