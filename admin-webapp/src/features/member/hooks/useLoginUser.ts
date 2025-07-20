import useSWR from 'swr'
import { useEffect, useState } from 'react'
import { Me } from '@/features/member/types/me'

export function useLoginUser() {
  const fetcher = (url: string) => fetch(url, { credentials: 'include' }).then((r) => r.json())

  const { data, error, isLoading } = useSWR(`https://localhost:8102/api/members/me`, fetcher)
  const [loginUser, setLoginUser] = useState<Me | null>(null)

  useEffect(() => {
    if (isLoading) return

    if (error) {
      alert('取得失敗')
      return
    }

    if (!error && (!data.data || Object.keys(data.data).length === 0)) {
      alert('データ空っぽ')
      return
    }

    if (data) {
      const me: Me = {
        userID: data.data.userID,
        email: data.data.email,
        name: data.data.name,
        logoPath: data.data.logoPath,
        role: data.data.role,
        updatedAt: data.data.updatedAt,
      }
      setLoginUser(me)
    }
  }, [data, error, isLoading])

  return { loginUser, isLoading }
}
