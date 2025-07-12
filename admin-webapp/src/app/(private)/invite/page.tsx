'use client'

import { useState } from 'react'

export default function InvitePage() {
  const [email, setEmail] = useState('')

  const handleSubmit = (e: React.FormEvent) => {
    fetch(`https://localhost:8102/api/auth/callback`)
    alert(`招待メールを送信しました: ${email}`)
  }

  return (
    <main className="mainContainer">
      <h1 className="pageTitle">InvitePage</h1>
      <div className="pageContent">
        <form onSubmit={handleSubmit} className="inviteForm">
          <input type="email" placeholder="メールアドレスを入力" value={email} onChange={(e) => setEmail(e.target.value)} required className="emailInput" />
          <button type="submit" className="submitBtn">
            招待する
          </button>
        </form>
      </div>
    </main>
  )
}
