import useSWR from 'swr'
import { Workspace } from '@/features/workspace/types/workspace'

export function useWorkspaces() {
  const fetcher = (url: string) => fetch(url, { credentials: 'include' }).then((r) => r.json())

  const { data, error, isLoading } = useSWR(`https://localhost:8102/api/workspaces/my-list`, fetcher)

  if (error) {
    // toast.error('メンバーの取得に失敗しました')
    return { workspaces: [], isLoading: false }
  }

  const workspaces: Workspace[] =
    data?.data?.map(($w: Workspace) => ({
      id: $w.id,
      name: $w.name,
      stripeCustomerId: $w.stripeCustomerId,
      createdAt: $w.createdAt,
      updatedAt: $w.updatedAt,
    })) ?? []

  return { workspaces, isLoading }
}
