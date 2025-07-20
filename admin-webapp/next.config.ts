import type { NextConfig } from 'next'

const nextConfig: NextConfig = {
  images: {
    domains: [
      's.gravatar.com', // ← これが必要
      'cdn.auth0.com', // ← fallback URL のドメインも一緒に追加
    ],
  },
  reactStrictMode: false,
}

export default nextConfig
