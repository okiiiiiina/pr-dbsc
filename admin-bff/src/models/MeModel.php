<?php

namespace App\models;

class MeModel
{
  private string $userID;
  private ?string $memberID;
  private string $name;
  private string $email;
  private string $role;


  public function __construct(array $data)
  {
    $this->userID = $data['userID'] ?? '';
    $this->memberID = $data['memberID'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->email = $data['email'] ?? '';
    $this->role = $data['role'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'userID' => $this->userID,
      'memberID' => $this->memberID,
      'name' => $this->name,
      'email' => $this->email,
      'role' => $this->role,
    ];
  }

  public function getUserID(): string
  {
    return $this->userID;
  }

  public function getMemberID(): string
  {
    return $this->userID;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getName(): string
  {
    return $this->name;
  }
}
