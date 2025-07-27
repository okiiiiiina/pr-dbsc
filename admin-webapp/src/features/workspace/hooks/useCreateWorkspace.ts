'use client'
import { useRouter } from 'next/navigation'
import useSWRMutation from 'swr/mutation'

async function post(url: string, { arg }: { arg: { name: string; plan: string } }) {
  return fetch(url, {
    method: 'POST',
    body: JSON.stringify(arg),
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
    },
  })
}

export function useCreateWorkspace() {
  const router = useRouter()

  const {
    trigger: createWorkspace,
    isMutating,
    error,
  } = useSWRMutation('https://localhost:8102/api/workspaces', post, {
    onSuccess: () => {
      alert('success')
      router.push('/')
    },
    onError: () => {
      alert('ワークスペースの切り替えに失敗しました')
    },
  })

  return { createWorkspace, isMutating, error }
}
