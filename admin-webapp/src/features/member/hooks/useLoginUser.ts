'use client'

import useSWR from 'swr'
import { useEffect } from 'react'
import { Me } from '@/features/member/types/me'
import { fetcher } from '@/libs/fetcher'
import { useRouter } from 'next/navigation'

export function useLoginUser() {
  const router = useRouter()

  const { data, error, isLoading } = useSWR(`https://localhost:8102/api/members/me`, fetcher)

  useEffect(() => {
    if (error || (data && Object.keys(data.data).length === 0)) {
      router.push('/login')
    }
  }, [error, data, router])

  const loginUser: Me | null = data?.data
    ? {
        userID: data.data.userID,
        email: data.data.email,
        name: data.data.name,
        logoPath: data.data.logoPath,
        role: data.data.role,
        updatedAt: data.data.updatedAt,
        workspace: data.data.workspace,
      }
    : null

  return { loginUser, isLoading }
}
