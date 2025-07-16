<?php

namespace App\repositories;


use App\storage\JsonLoader;

// require_once __DIR__ . '/../storage/func.php';

class UserRepository
{
  private string $storageFile;
  private JsonLoader $jsonLoader;

  public function __construct()
  {
    $this->storageFile = __DIR__ . '/../storage/user.json';
    $this->jsonLoader = new JsonLoader($this->storageFile);
  }

  public function findByID(string $id): ?array
  {
    $users = $this->jsonLoader->load();
    return $users[$id] ?? null;
  }

  /**
   * upsertUser
   */
  public function upsertUser(array $user): array
  {
    $id = $user['id'];
    $users = $this->jsonLoader->load();
    $users[$id] = $user;

    $result = file_put_contents(
      $this->storageFile,
      json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    if ($result === false) {
      return ['error' => true];
    }
    return [];
  }

  /**
   * fetchAll
   */
  public function fetchAll(): array
  {
    if (!file_exists($this->storageFile)) {
      return [];
    }

    $json = file_get_contents($this->storageFile);
    $assoc = json_decode($json, true); // キー付き配列

    if (!is_array($assoc)) {
      return [];
    }

    // キーを除去して [ { ... }, { ... } ] の形に変換
    return array_values($assoc);
  }
}
