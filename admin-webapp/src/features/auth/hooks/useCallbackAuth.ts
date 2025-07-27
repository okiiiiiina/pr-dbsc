'use client'

import useSWRMutation from 'swr/mutation'
import { useEffect } from 'react'
import { useRouter } from 'next/navigation'

async function postCallbackAuth(url: string, { arg }: { arg: { code: string } }) {
  const res = await fetch(url, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(arg),
  })

  if (!res.ok) throw new Error(await res.text())
  return res.json()
}

export function useCallbackAuth() {
  const router = useRouter()

  const { trigger } = useSWRMutation('https://localhost:8102/api/auth/callback', postCallbackAuth)

  useEffect(() => {
    console.log('🍎', 'useCallbackAuthのuseEffectです')

    const url = new URL(window.location.href)
    const code = url.searchParams.get('code')
    const authErr = url.searchParams.get('error')

    if (authErr) {
      alert('Auth error:' + authErr)
      // error toast
      return
    }

    if (!code) {
      alert('code パラメータがありません')
      // error toast
      return
    }

    trigger({ code })
      .then(async () => {
        const res = await fetch(`https://localhost:8102/api/workspaces/my-list`, {
          credentials: 'include',
        })

        if (!res.ok) {
          const text = res.text()
          console.error('🚨 API error response:', text)
          throw new Error('API returned non-OK response')
        }
        const data = await res.json()
        if (data.data.length > 0) {
          router.push('/workspace/select')
        } else {
          router.push('/workspace/create')
        }
      })
      .catch((e) => {
        console.error('☠️ エラー:', e)
      })
  }, [router])
}
