import { useEffect, useState } from 'react'
import { Member } from '../types/member'

export function useLoginUser() {
  const [loginUser, setLoginUser] = useState<Member | null>(null)
  const [status, setStatus] = useState<string>('loading...')

  useEffect(() => {
    async function fetchMe() {
      try {
        const res = await fetch(`https://localhost:8102/api/users/me`, {
          credentials: 'include',
        })
        if (!res.ok) throw new Error(`HTTP error ${res.status}`)
        const data = await res.json()

        const member: Member = {
          sub: data.data.sub,
          email: data.data.email,
          name: data.data.name,
          picture: data.data.picture,
          role: data.data.role,
          updatedAt: data.data.updated_at,
        }

        setLoginUser(member)
        setStatus('ok')
      } catch {
        setStatus('error')
      }
    }

    fetchMe()
  }, [])

  return { loginUser, status }
}
