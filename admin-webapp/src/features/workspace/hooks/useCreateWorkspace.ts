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
  }).then((res) => res.json())
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
      router.push('/workspace/create?refresh=1')
    },
    onError: () => {
      alert('error')
    },
  })

  return { createWorkspace, isMutating, error }

  // const router = useRouter()

  // const createWorkspace = async (name: string, plan: string) => {
  //   const res = await fetch('https://localhost:8102/api/workspaces', {
  //     method: 'POST',
  //     body: JSON.stringify({ name, plan }),
  //     credentials: 'include',
  //     headers: { 'Content-Type': 'application/json' },
  //   })
  //   if (!res.ok) throw new Error('Failed to create workspace')
  //   alert('success')
  //   router.push('/workspace/create?refresh=1')
  // }

  // return { createWorkspace }
}
