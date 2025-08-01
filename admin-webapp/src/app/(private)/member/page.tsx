'use client'

import { useMembers } from '@/features/member/hooks/useMembers'

export default function MemberPage() {
  console.log('🪏レンダリング🪏')

  const { members, isLoading } = useMembers()
  console.log(members)

  if (isLoading) return <main className="mainContainer">loading...</main>

  return (
    <main className="mainContainer">
      <h1 className="pageTitle">MemberPage</h1>
      <div className="pageContent">
        {members.length === 0 ? (
          <div className="text-gray-500 text-sm mt-4">メンバーがいません</div>
        ) : (
          <div className="memberTableWrapper">
            <table className="memberTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>メールアドレス</th>
                  <th>名前</th>
                  <th>権限</th>
                </tr>
              </thead>
              <tbody>
                {members.map((member) => (
                  <tr key={member.id}>
                    <td>{member.userID}</td>
                    <td>{member.email}</td>
                    <td>{member.name}</td>
                    <td>{member.role}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </main>
  )
}
