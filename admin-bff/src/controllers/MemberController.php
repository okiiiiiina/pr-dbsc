<?php

namespace App\controllers;

use App\core\error\CustomException;
use App\core\AuthContext;
use App\core\Response;
use App\services\MemberService;

use Exception;


require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../core/AuthContext.php';

class MemberController
{
  private MemberService $service;

  public function __construct(MemberService $service)
  {
    $this->service = $service;
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
   * メンバー一覧を返す
   */
  public function handleGetList(): void
  {
    $wsID = $_COOKIE['Workspace_id'] ?? null;

    if (!$wsID) {
      Response::error('workspace_id が cookie に存在しません', 400);
      return;
    }

    try {
      $members = $this->service->getAll($wsID);

      $list = array_map(
        fn($member) => $member->toArray(),
        $members
      );

      error_log("🍎🍎🍎" . json_encode($list, true));

      Response::success($list);
    } catch (Exception $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
        $e->getTraceAsString()
      );
    }

    Response::success($memberList);
  }
}
