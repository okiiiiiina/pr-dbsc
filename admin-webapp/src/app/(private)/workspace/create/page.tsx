'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'

export default function WorkspaceCreatePage() {
  console.log('🪏レンダリング🪏')

  const router = useRouter()

  const [name, setName] = useState('')
  const [selectedPlan, setSelectedPlan] = useState<'pro' | 'business' | ''>('pro')

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    try {
      const res = await fetch('https://localhost:8102/api/workspaces', {
        method: 'POST',
        body: JSON.stringify({
          name: name,
          plan: selectedPlan,
        }),
        credentials: 'include',
      })
      alert('success')
      router.push('/workspace/create?refresh=1')

      // alert(`名前: ${name}\nプラン: ${selectedPlan === 'pro' ? 'プロプラン' : selectedPlan === 'business' ? 'ビジネスプラン' : '未選択'}`)
    } catch (e) {
      console.log(e)
      alert('error')
    }
  }

  return (
    <main className="mainContainer">
      <h1 className="pageTitle">WorkspaceCreatePage</h1>
      <div className="pageContent">
        <form className="workspaceForm">
          {/* 名前入力 */}
          <div>
            <label className="label">ワークスペース名</label>
            <input type="text" value={name} onChange={(e) => setName(e.target.value)} className="input" placeholder="例: デザイン部" />
          </div>

          {/* プラン選択 */}
          <div>
            <div className="label">プラン選択</div>
            <label className="block">
              <input type="radio" name="plan" value="pro" checked={selectedPlan === 'pro'} onChange={() => setSelectedPlan('pro')} className="mr-2" />
              プロプラン
            </label>

            <label className="block">
              <input type="radio" name="plan" value="business" checked={selectedPlan === 'business'} onChange={() => setSelectedPlan('business')} className="mr-2" />
              ビジネスプラン
            </label>
          </div>

          {/* 作成ボタン */}
          <button onClick={handleSubmit} className="submitBtn" disabled={name == '' || selectedPlan == ''}>
            新規作成
          </button>
        </form>
      </div>
    </main>
  )
}
