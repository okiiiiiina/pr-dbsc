<?php

class UserModel
{
  public string $sub;
  public string $email;
  public string $name;
  public string $nickname;
  public string $logoPath;
  public string $updatedAt;
  public string $role;
  public bool $emailVerified;
  public string $refreshToken;

  public function __construct(array $data)
  {
    $this->sub = $data['sub'] ?? '';
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
      'sub' => $this->sub,
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
