'use client'

import { useEffect } from 'react'
import { useRouter } from 'next/navigation'

export default function CallbackPage() {
  const router = useRouter()

  useEffect(() => {
    (async () => {
      const url      = new URL(window.location.href)
      const code     = url.searchParams.get('code')
      const authErr  = url.searchParams.get('error')

      if (authErr) {
        console.error('Auth error:', authErr)
        return
      }
      if (!code) {
        console.error('code パラメータがありません')
        return
      }

      try {
        console.log("🍎codetoken変換");
        const res = await fetch(`https://localhost:8102/api/auth/callback`,
          {
            method: 'POST',
            credentials: 'include',
            headers:   { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code }),
          },
        )

        if (!res.ok) {
          console.error('❌ トークン取得失敗:', await res.text())
          return
        }

        const data = await res.json()
        console.log('✅ Token:', data)

        router.push('/')
      } catch (e) {
        console.error('🚨 ネットワークエラー:', e)
      }
    })()
  }, [router])

  return (
    <div>
      <h1>callback</h1>
    </div>
  )
}
