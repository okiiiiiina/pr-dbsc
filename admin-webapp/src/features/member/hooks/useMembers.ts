import { useEffect, useState } from 'react'
import { Member } from '../types/member'

export function useMembers() {
  const [members, setMembers] = useState<Member[]>([])

  useEffect(() => {
    async function fetchMembers() {
      try {
        const res = await fetch(`https://localhost:8102/api/users`, {
          credentials: 'include',
        })
        if (!res.ok) throw new Error(`HTTP error ${res.status}`)
        const data = await res.json()

        console.log('☠️error:', data)

        const formattedMembers: Member[] = data.data.map((m: any) => ({
          sub: m.sub,
          email: m.email,
          name: m.name,
          picture: m.picture,
          role: m.role,
          updatedAt: m.updated_at,
        }))

        setMembers(formattedMembers)
      } catch (e) {
        // エラートースト出す
        console.log('☠️error:', e)
      }
    }

    fetchMembers()
  }, [])

  return { members }
}
