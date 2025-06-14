<?php

class UserRepository
{
  private string $storageFile = __DIR__ . '/../storage/users.json';

  public function findBySub(string $sub): ?array
  {
    $users = $this->loadUsers();
    return $users[$sub] ?? null;
  }

  /**
   * upsertUser
   */
  public function upsertUser(array $userInfo, string $refreshToken): void
  {
    $sub = $userInfo['sub'];
    $users = $this->loadUsers();

    $users[$sub] = [
      'sub' => $sub,
      'email' => $userInfo['email'] ?? '',
      'name' => $userInfo['name'] ?? '',
      'picture' => $userInfo['picture'] ?? '',
      'updated_at' => date('Y-m-d H:i:s'),
      'role' => 'owner',
      'refresh_token' => $refreshToken
    ];

    file_put_contents(
      $this->storageFile,
      json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
  }

  /**
   * loadUsers
   */
  private function loadUsers(): array
  {
    if (!file_exists($this->storageFile)) return [];
    return json_decode(file_get_contents($this->storageFile), true) ?? [];
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
