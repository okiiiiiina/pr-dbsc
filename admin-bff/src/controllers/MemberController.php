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
    $me = AuthContext::getMe();

    if (!$me) {
      Response::error('Unauthorized', 401);
      return;
    }

    Response::success($me->toArray());
  }

  /**
   * メンバー一覧を返す
   */
  public function handleGetList(): void
  {
    $wsID = $_COOKIE['Workspace_ID'] ?? null;

    if (!$wsID) {
      Response::error('Workspace_ID が cookie に存在しません', 400);
      return;
    }

    try {
      $members = $this->service->getAll($wsID);

      $list = array_map(
        fn($member) => $member->toArray(),
        $members
      );

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
