<?php

namespace App\models;

class UserModel
{
  public string $userID;
  public string $email;
  public string $name;
  public string $nickname;
  public string $logoPath;
  public string $updatedAt;
  public string $role;
  public bool $emailVerified;
  public string $refreshToken;

  // 本来は一つ一つ値渡すのがいいと思うけど、変数増えるとそれだけ面倒なのでarrayに集約
  public function __construct(array $data)
  {
    $this->userID = $data['sub'] ?? '';
    $this->email = $data['email'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->nickname = $data['nickname'] ?? '';
    $this->logoPath = $data['logoPath'] ?? '';
    $this->updatedAt = $data['updatedAt'] ?? '';
    $this->role = $data['role'] ?? '';
    $this->emailVerified = $data['email_verified'] ?? false;
    $this->refreshToken = $data['refreshToken'] ?? false;
  }

  public function toArray(): array
  {
    return [
      'userID' => $this->userID,
      'email' => $this->email,
      'name' => $this->name,
      'nickname' => $this->nickname,
      'logoPath' => $this->logoPath,
      'updatedAt' => $this->updatedAt,
      'role' => $this->role,
      'email_verified' => $this->emailVerified,
      'refreshToken' => $this->refreshToken,
    ];
  }
}
