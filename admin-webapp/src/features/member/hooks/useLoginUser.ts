import { useEffect, useState } from 'react'
import { Me } from '@/features/member/types/me'

export function useLoginUser() {
  const [loginUser, setLoginUser] = useState<Me | null>(null)
  const [status, setStatus] = useState<string>('loading...')

  useEffect(() => {
    async function fetchMe() {
      try {
        const res = await fetch(`https://localhost:8102/api/users/me`, {
          credentials: 'include',
        })
        if (!res.ok) throw new Error(`HTTP error ${res.status}`)
        const data = await res.json()

        const me: Me = {
          sub: data.data.sub,
          email: data.data.email,
          name: data.data.name,
          logoPath: data.data.logoPath,
          role: data.data.role,
          updatedAt: data.data.updatedAt,
        }

        setLoginUser(me)
        setStatus('ok')
      } catch {
        setStatus('error')
      }
    }

    fetchMe()
  }, [])

  return { loginUser, status }
}
