'use client'
import useSWRMutation from 'swr/mutation'
import { useRouter } from 'next/navigation'

async function switchWorkspace(url: string, { arg }: { arg: { id: string } }) {
  const res = await fetch(url, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(arg),
  })

  if (!res.ok) {
    const text = await res.text()
    throw new Error(text)
  }
  return
}

export function useSwitch() {
  const router = useRouter()

  const { trigger, isMutating, error } = useSWRMutation('https://localhost:8102/api/workspaces/switch', switchWorkspace, {
    onSuccess: () => {
      router.push('/')
    },
    onError: () => {
      alert('error')
    },
  })

  return { switchWorkspace: trigger, isMutating, error }
}
