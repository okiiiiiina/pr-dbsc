'use client'

import { useHealthcheck } from '../features/common/hooks/useHealthcheck'
import { useLoginUser } from '../features/member/hooks/useLoginUser'

// import Image from 'next/image'

export default function Home() {
  const { loginUser, status: userStatus } = useLoginUser()
  const healthStatus = useHealthcheck()

  return (
    <main className="mainContainer">
      <div className="healthStatus">✅ Healthcheck: {healthStatus}</div>
      <div className="userStatus">🍎 LoginUser Status: {userStatus}</div>

      {loginUser ? (
        <table className="loginUserTable">
          <thead>
            <tr>
              <th>Key</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            {Object.entries(loginUser).map(([key, value]) => (
              <tr key={key}>
                <td>{key}</td>
                <td>
                  {key === 'picture' ? (
                    <img src={value as string} alt="avatar" className="userAvatar" />
                  ) : // <Image
                  //   src={value as string}
                  //   alt="avatar"
                  //   width={40}
                  //   height={40}
                  //   className="userAvatar"
                  // />
                  typeof value === 'boolean' ? (
                    (value as boolean).toString()
                  ) : (
                    (value as string)
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      ) : (
        <div className="notLoggedIn">Not logged in</div>
      )}
    </main>
  )
}
