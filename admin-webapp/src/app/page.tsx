'use client'

import { useEffect, useState } from 'react'

export default function Home() {
  const [status, setStatus] = useState<string>('loading...')

  useEffect(() => {
    fetch(`https://localhost:8102/api/health`)
      .then(res => res.json())
      .then(data => setStatus(data.message || 'ok'))
      .catch(() => setStatus('error'))
  }, [])

  return (
    <main className="flex items-center justify-center min-h-screen font-mono text-xl">
      Healthcheck: {status}
    </main>
  )
}
