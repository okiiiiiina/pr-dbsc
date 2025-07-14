'use client'

import { useState } from 'react'
import { useCreateWorkspace } from '@/features/workspace/hooks/useCreateWorkspace'

export default function WorkspaceCreatePage() {
  console.log('🪏レンダリング🪏')

  const { createWorkspace, isMutating } = useCreateWorkspace()

  const [name, setName] = useState('')
  const [selectedPlan, setSelectedPlan] = useState<'pro' | 'business' | ''>('pro')

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    createWorkspace({ name, plan: selectedPlan })
  }

  console.log('isMutating:', isMutating)

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
