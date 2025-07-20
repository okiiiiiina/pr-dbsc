<?php

namespace App\controllers;

use App\core\AuthContext;
use App\core\Response;

use App\services\UserService;

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../core/AuthContext.php';

class MemberController
{
  private UserService $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  /**
   * ログインユーザーの情報を返す
   */
  public function handleGetMe(): void
  {
    error_log("🍆🍆🍆handleGetMe s🍆🍆🍆");
    $user = AuthContext::getMe();

    if (!$user) {
      error_log("🍆con !user");
      Response::error('Unauthorized', 401);
      return;
    }

    Response::success($user);
  }

  /**
   * ユーザー一覧を返す
   */
  public function handleGetUsers(): void
  {
    $users = $this->userService->getAllUsers();

    Response::success($users);
  }
}
