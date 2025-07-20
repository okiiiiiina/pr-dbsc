import useSWRMutation from 'swr/mutation'

async function switchWorkspace(url: string, { arg }: { arg: { id: string } }) {
  await fetch(url, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(arg),
  })
}

export function useSwitch() {
  const { trigger, isMutating, error } = useSWRMutation('/workspaces/switch', switchWorkspace)

  return { switchWorkspace: trigger, isMutating, error }
}
