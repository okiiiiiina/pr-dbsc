<?php

class Response
{
  public static function json($data, $code = 200, $e = null)
  {
    if ($e instanceof Throwable) {
      error_log("[" . date('Y-m-d H:i:s') . "] Exception: " . $e->getMessage());
      error_log($e->getTraceAsString());
    }

    http_response_code($code);
    header('Access-Control-Allow-Origin: https://localhost:3101');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    echo json_encode([
      'status' => $code < 400 ? 'ok' : 'error',
      'code' => $code,
      'data' => $data,
    ], JSON_UNESCAPED_SLASHES);
    exit;
  }

  public static function error($message, $code = 400)
  {
    self::json(['message' => $message], $code);
  }

  public static function success($data = [], $code = 200)
  {
    self::json($data, $code);
  }
}
