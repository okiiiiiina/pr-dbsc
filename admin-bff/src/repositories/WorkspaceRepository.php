<?php

namespace App\repositories;

use App\core\error\CustomException;
use App\models\WorkspaceModel;

use App\core\JsonLoader;

class WorkspaceRepository
{
  private string $storagePath;
  private string $memStoragePath;

  public function __construct()
  {
    $this->storagePath = __DIR__ . '/../storage/workspace.json';
    $this->memStoragePath = __DIR__ . '/../storage/member.json';
  }

  /**
   * findByID
   */
  public function findByID(string $id): array
  {
    $wsJsonLoader = new JsonLoader($this->storagePath);
    $workspaces = $wsJsonLoader->load();

    if ($workspaces[$id] === null) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to store workspace data in the storage file.');
    }

    return $workspaces[$id];
  }

  /**
   * getMyList
   */
  public function getMyList(string $userID): array
  {
    // mem
    $memJsonLoader = new JsonLoader($this->memStoragePath);
    $members = $memJsonLoader->load();

    $workspaceIDs = array_column(
      array_filter($members, fn($m) => $m['userID'] === $userID),
      'workspaceID'
    );

    // ws
    $wsJsonLoader = new JsonLoader($this->storagePath);
    $workspaces = $wsJsonLoader->load();

    $myList = [];
    foreach ($workspaceIDs as $id) {
      if (isset($workspaces[$id])) {
        $myList[] = $workspaces[$id];
      }
    }

    return $myList;
  }

  /**
   * create
   */
  public function create(
    WorkspaceModel $ws,
  ): void {
    $jsonLoader = new JsonLoader($this->storagePath);
    $workspaces = $jsonLoader->load();
    $workspaces[$ws->getID()] = $ws->toArray();

    $result = file_put_contents(
      $this->storagePath,
      json_encode($workspaces, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    if ($result === false) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to write workspace data to storage file');
    }
  }
}
