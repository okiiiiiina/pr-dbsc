<?php
require_once __DIR__ . '/../core/Response.php';

class AuthMiddleware {
  public static function handle() {
    echo("🍆 AuthMiddleware:");
    session_start();
    if (!isset($_SESSION['user'])) {
      Response::error('Unauthorized', 401);
    }
  }
}
