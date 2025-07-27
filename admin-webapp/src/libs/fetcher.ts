import Router from 'next/router'

export const fetcher = async (url: string) => {
  const res = await fetch(url, {
    credentials: 'include',
  })
  console.log('🍎 fetcher 🍎 status:', res.status)

  if (res.status === 401 || res.status === 403) {
    if (typeof window !== 'undefined' && window.location.pathname !== '/login') {
      Router.replace('/login')
      return
    }
    throw new Error('Unauthorized')
  }

  if (!res.ok) {
    throw new Error('Fetch failed')
  }

  return res.json()
}
