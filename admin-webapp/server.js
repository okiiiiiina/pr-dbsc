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


// import fs from 'node:fs';
// import path from 'node:path';
// import { parse } from 'url';
// import https from 'node:https';
// import next from 'next';
// import { fileURLToPath } from 'url';

// // __dirname の代替（ESM用）
// const __filename = fileURLToPath(import.meta.url);
// const __dirname = path.dirname(__filename);

// const port = 3101;
// const dev = true;
// const app = next({ dev, dir: './src' });
// const handle = app.getRequestHandler();

// const httpsOptions = {
//   key: fs.readFileSync(path.resolve(__dirname, '../certs/localhost-key.pem')),
//   cert: fs.readFileSync(path.resolve(__dirname, '../certs/localhost.pem')),
// };

// app.prepare().then(() => {
//   https.createServer(httpsOptions, (req, res) => {
//     if (!req.url) {
//       const parsedUrl = parse(req.url, true);
//       handle(req, res, parsedUrl);
//     }
//   }).listen(port, () => {
//     console.log(`✅ HTTPS server ready at https://localhost:${port}`);
//   });
// });
