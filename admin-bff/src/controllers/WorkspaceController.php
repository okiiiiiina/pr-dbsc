<?php

require_once __DIR__ . '/../services/WorkspaceService.php';
require_once __DIR__ . '/../core/AuthContext.php';

class WorkspaceController
{
  private WorkspaceService $workspaceService;

  public function __construct(WorkspaceService $workspaceService)
  {
    $this->workspaceService = $workspaceService;
  }

  // /**
  //  * handleCreate
  //  */
  // public function handleCreate(): void
  // {
  //   $body = file_get_contents('php://input');
  //   $data = json_decode($body, true)

  //   // Response::success($user);
  // }
}
