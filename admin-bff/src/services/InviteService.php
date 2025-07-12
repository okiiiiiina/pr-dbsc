<?php

namespace App\services;

class InviteService
{

  public function __construct() {}

  public function invite(string $email)
  {
    error_log("🍏" . json_encode($email));
    // メールのバリデーション
    // メール送信
    // Stripeにメンバー1名追加
    // DB更新、member
  }
}
