<?php

namespace App\controllers;

use App\core\AuthContext;
use App\core\Response;

use App\services\UserService;

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../core/AuthContext.php';

class UserController
{
  private UserService $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
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
