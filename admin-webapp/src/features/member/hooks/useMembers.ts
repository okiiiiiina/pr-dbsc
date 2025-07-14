import useSWR from 'swr'
import { useEffect, useState } from 'react'
import { Member } from '@/features/member/types/member'

export function useMembers() {
  const fetcher = (url: string) => fetch(url, { credentials: 'include' }).then((r) => r.json())

  const { data, error, isLoading } = useSWR(`https://localhost:8102/api/users`, fetcher)
  const [members, setMembers] = useState<Member[]>([])

  useEffect(() => {
    if (isLoading) return

    if (error) {
      // toast.error('メンバーの取得に失敗しました')
      return
    }

    console.log(data)

    if (data.data.length > 0) {
      setMembers(
        data.data.map((m: any) => ({
          userID: m.userID,
          email: m.email,
          name: m.name,
          picture: m.picture,
          role: m.role,
          updatedAt: m.updatedAt,
          workspace: undefined,
        }))
      )
    }
  }, [data, error, isLoading])

  return { members, isLoading }
}
