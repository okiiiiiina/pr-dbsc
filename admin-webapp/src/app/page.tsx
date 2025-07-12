'use client'

import { useHealthcheck } from '../features/common/hooks/useHealthcheck'
import { useLoginUser } from '../features/member/hooks/useLoginUser'

import { Me } from '../features/member/types/me'
import Link from 'next/link'

// import Image from 'next/image'

export default function Home() {
  console.log('🪏レンダリング🪏')

  const { loginUser } = useLoginUser()
  const healthStatus = useHealthcheck()

  const keys: (keyof Me)[] = ['userID', 'email', 'name', 'logoPath', 'role', 'updatedAt']

  return (
    <main className="mainContainer">
      <div className="loginUserInfo">
        {loginUser && <img src={loginUser.logoPath} alt="avatar" className="topUserIcon" />}

        <div>
          <div className="healthStatus">✅ Healthcheck: {healthStatus}</div>
          <div className="userStatus">🍎 LoginUser Status: {loginUser ? 'ok' : 'error'}</div>
        </div>

        {loginUser ? (
          <table className="loginUserTable">
            <thead>
              <tr>
                <th>Key</th>
                <th>Value</th>
              </tr>
            </thead>
            <tbody>
              {keys.map((key) => {
                const value: any = loginUser[key]
                const typedValue = value as string | boolean

                return (
                  <tr key={key}>
                    <td>{key}</td>
                    <td>{typeof typedValue === 'boolean' ? typedValue.toString() : value}</td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        ) : (
          <>
            <div className="notLoggedIn">Not logged in</div>
            <Link href="/login" className="loginBtn">
              ログイン
            </Link>
          </>
        )}
      </div>
    </main>
  )
}
