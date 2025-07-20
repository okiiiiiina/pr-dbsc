<?php

namespace App\controllers;

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
    $workspaces = $this->service->getMyList();

    $res = array_map(fn($w) => $w->toArray(), $workspaces);

    Response::success($res, 200);
  }

  /**
   * handleCreate
   */
  public function handleCreate(): void
  {
    $body = file_get_contents('php://input');
    $data = new CreateRequest(json_decode($body, true));

    $wsID = $this->service->create($data);

    setcookie('workspace_id', $wsID);
    Response::success([], 204);
  }

  /**
   * handleSelect
   */
  public function handleSwitch(): void
  {
    $body = file_get_contents('php://input');
    $data = new SelectRequest(json_decode($body, true));

    $id = $this->service->switch($data['id']);
    setcookie('workspace_id', $id);

    Response::success([], 204);
  }
}
