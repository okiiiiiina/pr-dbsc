<?php

namespace App\controllers;

use App\core\Response;
use App\dto\request\workspace\CreateRequest;
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
   * handleCreate
   */
  public function handleCreate(): void
  {
    $body = file_get_contents('php://input');
    $data = new CreateRequest(json_decode($body, true));

    $this->service->create($data);

    Response::success([], 204);
  }
}
