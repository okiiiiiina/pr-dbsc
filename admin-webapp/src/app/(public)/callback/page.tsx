'use client'

import { useCallbackAuth } from '@/features/auth/hooks/useCallbackAuth'

export default function CallbackPage() {
  useCallbackAuth()

  return (
    <div>
      <h1>callback</h1>
    </div>
  )
}
