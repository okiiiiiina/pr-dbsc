'use client'

import { useEffect } from 'react'
import { useRouter } from 'next/navigation'

export default function CallbackPage() {
  const router = useRouter()

  useEffect(() => {
    const fetchToken = async () => {
      const params = new URLSearchParams(window.location.search)
      const code = params.get('code')
      const error = params.get('error')

      if (error) {
        console.error('Auth error:', error)
        return
      }

      if (!code) {
        console.error('codeがありません')
        return
      }

      try {
        console.log("🍎codetoken変換");

        const res = await fetch(`https://localhost:8102/api/auth/callback`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            code,
          }),
        })

        if (!res.ok) {
          const text = await res.text()
          console.error('❌ Token fetch failed:', text)
          return
        }

        const data = await res.json()
        console.log('✅ Token:', data)

        router.push('/')
      } catch (err) {
        console.error('🚨 リクエスト失敗:', err)
      }
    }

    fetchToken()
  }, [router])

  return (
    <div>
      <h1>callback</h1>
    </div>
  )
}
