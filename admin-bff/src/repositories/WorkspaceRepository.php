<?php

namespace App\repositories;

use App\core\error\CustomException;
use App\models\WorkspaceModel;

use App\storage\JsonLoader;;

class WorkspaceRepository
{
  private string $storageFile;
  private JsonLoader $jsonLoader;

  public function __construct()
  {
    $this->storageFile = __DIR__ . '/../storage/workspace.json';
    $this->jsonLoader = new JsonLoader($this->storageFile);
  }

  public function create(
    WorkspaceModel $ws,
  ): void {
    $workspaces = $this->jsonLoader->load();
    $workspaces[$ws->getID()] = $ws->toArray();

    $result = file_put_contents(
      $this->storageFile,
      json_encode($workspaces, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    if ($result === false) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to write workspace data to storage file');
    }
  }
}
