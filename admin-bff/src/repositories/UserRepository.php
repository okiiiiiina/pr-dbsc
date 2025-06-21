<?php

require_once __DIR__ . '/../storage/func.php';

class UserRepository
{
  private string $storageFile;
  private JsonLoader $jsonLoader;

  public function __construct()
  {
    $this->storageFile = __DIR__ . '/../storage/users.json';
    $this->jsonLoader = new JsonLoader($this->storageFile);
  }

  public function findBySub(string $sub): ?array
  {
    $users = $this->jsonLoader->load();
    return $users[$sub] ?? null;
  }

  /**
   * upsertUser
   */
  public function upsertUser(array $user): void
  {
    $sub = $user['sub'];
    $users = $this->jsonLoader->load();
    $users[$sub] = $user;

    file_put_contents(
      $this->storageFile,
      json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
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
