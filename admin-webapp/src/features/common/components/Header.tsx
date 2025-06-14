'use client'

import Link from 'next/link'

export default function Header() {
  return (
    <header className="appHeader">
      <div className="headerInner">
        <h1 className="headerLogo">
          <Link href="/" className="headerLink">
            practice
          </Link>
        </h1>
        <nav className="headerNav">
          <Link href="/member" className="headerLink">
            メンバー
          </Link>
          <Link href="/billing" className="headerLink">
            請求
          </Link>
          <Link href="/plan" className="headerLink">
            プラン
          </Link>
          <Link href="/invite" className="headerLink">
            招待
          </Link>
        </nav>
      </div>
    </header>
  )
}
