import { useEffect, useState } from 'react'

export function useHealthcheck() {
  const [status, setStatus] = useState<string>('loading...')

  useEffect(() => {
    async function check() {
      try {
        const res = await fetch(`https://localhost:8102/api/health`, {
          credentials: 'include',
        });
        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
        const data = await res.json();
        setStatus(data.message || 'ok');
      } catch {
        setStatus('error');
      }
    }

    check();
  }, []);

  return status;
}
