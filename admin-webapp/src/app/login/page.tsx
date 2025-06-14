'use client'

export default function LoginPage() {

const handleLogin = async () => {
  try {
    console.log('API URL:', process.env.NEXT_PUBLIC_API_BASE_URI);
    const res = await fetch(`https://localhost:8102/api/auth/google-sso`)
    const data = await res.json()
    console.log("🍎", data.data.url);

    if (data?.data.url) {
      window.location.href = data.data.url
    }
  } catch (err) {
    console.error('Failed to load SSO URL', err)
  }
}

  return (
    <main
      style={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        minHeight: '100vh',
        padding: '2rem',
        fontFamily: 'monospace',
        backgroundColor: '#f9fafb',
      }}
    >
      <h1
        style={{
          fontSize: '1.75rem',
          marginBottom: '1.5rem',
          color: '#111827',
        }}
      >
        Sign in with Google
      </h1>
      <button
        onClick={handleLogin}
        style={{
          backgroundColor: '#1d4ed8',
          color: '#fff',
          padding: '0.75rem 2rem',
          fontSize: '1rem',
          border: 'none',
          borderRadius: '0.375rem',
          cursor: 'pointer',
          transition: 'background-color 0.2s ease-in-out',
        }}
        onMouseEnter={e => (e.currentTarget.style.backgroundColor = '#1e40af')}
        onMouseLeave={e => (e.currentTarget.style.backgroundColor = '#1d4ed8')}
      >
        Sign In
      </button>
    </main>
  )
}
