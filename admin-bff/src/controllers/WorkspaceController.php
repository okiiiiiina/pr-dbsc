<?php

namespace App\controllers;

use App\core\error\CustomException;
use App\core\Response;
use App\dto\request\workspace\CreateRequest;
use App\dto\request\workspace\SelectRequest;
use App\services\WorkspaceService;

require_once __DIR__ . '/../core/AuthContext.php';

class WorkspaceController
{
  private WorkspaceService $service;

  public function __construct(WorkspaceService $service)
  {
    $this->service = $service;
  }

  /**
   * handleGetMyList
   */
  public function handleGetMyList(): void
  {
    try {
      $workspaces = $this->service->getMyList();
      $res = array_map(fn($w) => $w->toArray(), $workspaces);
      Response::success($res, 200);
    } catch (CustomException $e) {
      Response::error($e->getMessage(), $e->getCode() ?: 500);
    } catch (\Throwable $e) {
      Response::error('Internal Server Error', 500);
    }
  }

  /**
   * handleCreate
   */
  public function handleCreate(): void
  {
    $body = file_get_contents('php://input');
    $data = new CreateRequest(json_decode($body, true));

    $wsID = $this->service->create($data);

    setcookie('Workspace_ID', $wsID);
    Response::success([], 204);
  }

  /**
   * handleSelect
   */
  public function handleSwitch(): void
  {
    try {
      $body = file_get_contents('php://input');
      $data = new SelectRequest(json_decode($body, true));

      $id = $this->service->switch($data->id);

      // setcookie('Workspace_ID', $id);
      setcookie('Workspace_ID', $id, [
        'expires' => time() + 3600,
        'path' => '/',
        'secure' => true,        // HTTPSのみ
        'httponly' => false,     // JSからも見えるならfalse
        'samesite' => 'None',    // クロスサイト用
      ]);
      Response::success([], 204);
    } catch (CustomException $e) {
      // すでに CustomException ならログ出てる想定
      Response::error($e->getMessage(), $e->getCode());
    } catch (\Throwable $e) {
      // 予期せぬエラーも握って落とさないように
      error_log("💥 未処理エラー: " . $e->getMessage());
      Response::error("Internal Server Error", 500);
    }
  }
}
