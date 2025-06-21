<?php

class WorkspaceService
{
  private WorkspaceRepository $repo;

  public function __construct(WorkspaceRepository $repo)
  {
    $this->repo = $repo;
  }

  public function getWorkspace(): void {}

  // public function getWorkspace(): array
  // {
  //   $rawWorkspaces = $this->repo->fetchAll();
  //   $workspaces = [];

  //   foreach ($rawWorkspaces as $workspaceData) {
  //     $workspaces[] = (new WorkspaceModel($workspaceData))->toArray();
  //   }

  //   return $workspaces;
  // }
}
