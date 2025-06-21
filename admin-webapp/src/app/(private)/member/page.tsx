'use client'

import { useMembers } from '../../../features/member/hooks/useMembers'

export default function MemberPage() {
  const { members } = useMembers()

  return (
    <main className="mainContainer">
      <h1 className="pageTitle">MemberPage</h1>

      {members.length === 0 ? (
        <div className="text-gray-500 text-sm mt-4">メンバーがいません</div>
      ) : (
        <div className="memberTableWrapper">
          <table className="memberTable">
            <thead>
              <tr>
                <th>メールアドレス</th>
                <th>名前</th>
                <th>権限</th>
                <th>更新日時</th>
              </tr>
            </thead>
            <tbody>
              {members.map((member) => (
                <tr key={member.sub}>
                  <td>{member.email}</td>
                  <td>{member.name}</td>
                  <td>{member.role}</td>
                  <td>{member.updatedAt}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </main>
  )
}
