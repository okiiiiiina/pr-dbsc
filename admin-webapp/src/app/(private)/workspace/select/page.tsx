'use client'

import Link from 'next/link'

export default function WorkspaceSelectPage() {
  console.log('🪏レンダリング🪏')

  const workspaces = [
    { id: 'design', name: 'デザイン部' },
    { id: 'dev', name: '開発チーム' },
    { id: 'marketing', name: 'マーケティング' },
  ]

  const handleClick = (id: string) => {
    alert(id)
  }

  return (
    <main className="mainContainer">
      <h1 className="pageTitle">WorkspaceSelectPage</h1>

      <div className={'pageContent'}>
        <ul className="workspaceSelectList">
          {workspaces.map((ws, idx) => (
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
