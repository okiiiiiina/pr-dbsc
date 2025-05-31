<?php
// require_once __DIR__ . '/../models/DbscKeyStore.php';

// class DbscMiddleware
// {
//   public static function handle(): void
//   {
//     $idHeader  = $_SERVER['HTTP_SEC_SESSION_ID']  ?? null;
//     $sigHeader = $_SERVER['HTTP_SEC_SESSION_SIGNATURE'] ?? null;

//     if (!$idHeader || !$sigHeader) {
//       http_response_code(401);
//       echo 'DBSC header missing';
//       exit;
//     }

//     $pubKey = DbscKeyStore::get($idHeader);
//     if (!$pubKey) {
//       http_response_code(401);
//       echo 'Session not registered';
//       exit;
//     }

//     $verified = openssl_verify($idHeader, base64_decode($sigHeader), $pubKey, OPENSSL_ALGO_SHA256);
//     if ($verified !== 1) {
//       http_response_code(401);
//       echo 'Invalid DBSC signature';
//       exit;
//     }
//     // 検証 OK → 次の処理へ
//   }
// }

require_once __DIR__ . '/../models/DbscKeyStore.php';

class DbscMiddleware
{
  public static function handle(): void
  {
    $sid = $_SERVER['HTTP_SEC_SESSION_ID']        ?? null;
    $sig = $_SERVER['HTTP_SEC_SESSION_SIGNATURE'] ?? null;

    if (!$sid || !$sig) {
      http_response_code(401);
      echo 'DBSC header missing';
      exit;
    }

    $pubKey = DbscKeyStore::get($sid);
    if (!$pubKey || openssl_verify($sid, base64_decode($sig), $pubKey, OPENSSL_ALGO_SHA256) !== 1) {
      http_response_code(401);
      echo 'Invalid DBSC signature';
      exit;
    }
  }
}
