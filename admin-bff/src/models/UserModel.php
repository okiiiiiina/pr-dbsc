<?php

class UserModel
{
  public string $sub;
  public string $email;
  public string $name;
  public string $nickname;
  public string $picture;
  public string $updatedAt;
  public string $role;
  public bool $emailVerified;
  public string $refreshToken;

  public function __construct(array $data, string $refreshToken = '')
  {
    $this->sub = $data['sub'] ?? '';
    $this->email = $data['email'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->nickname = $data['nickname'] ?? '';
    $this->picture = $data['picture'] ?? '';
    $this->updatedAt = $data['updated_at'] ?? '';
    $this->role = $data['role'] ?? '';
    $this->emailVerified = $data['email_verified'] ?? false;
    $this->refreshToken = $refreshToken;
  }

  public function toArray(): array
  {
    return [
      'sub' => $this->sub,
      'email' => $this->email,
      'name' => $this->name,
      'nickname' => $this->nickname,
      'picture' => $this->picture,
      'updated_at' => $this->updatedAt,
      'role' => $this->role,
      'email_verified' => $this->emailVerified,
      'refresh_token' => $this->refreshToken,
    ];
  }
}
