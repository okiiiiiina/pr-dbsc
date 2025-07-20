'use client'

import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { Workspace } from '@/features/workspace/types/workspace'
import { useWorkspaces } from '@/features/workspace/hooks/useWorkspaces'
import { useSwitch } from '@/features/workspace/hooks/useSwitch'

export default function WorkspaceSelectPage() {
  const { workspaces, isLoading } = useWorkspaces()
  const { switchWorkspace } = useSwitch()
  const router = useRouter()

  const handleClick = async (id: string) => {
    await switchWorkspace({ id })
  }

  if (isLoading) return <main className="mainContainer">loading...</main>

  return (
    <main className="mainContainer">
      <h1 className="pageTitle">WorkspaceSelectPage</h1>

      <div className={'pageContent'}>
        <ul className="workspaceSelectList">
          {workspaces.map((ws: Workspace, idx: number) => (
            <li key={idx} className="workspaceSelectItem" onClick={() => handleClick(ws.id)}>
              {ws.name}
            </li>
          ))}
        </ul>

        <Link href={'/workspace/create'} className="submitBtn">
          新しいワークスペースを作成
        </Link>
      </div>
    </main>
  )
}
