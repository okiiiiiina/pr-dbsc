<?php

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
   * ログインユーザーの情報を返す
   */
  public function handleGetMe(): void
  {
    $user = AuthContext::getUser();

    if (!$user) {
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
