<?php

namespace App\models;

class MeModel
{
  private string $userID;
  private string $memberID;
  private string $name;
  private string $email;


  public function __construct(array $data)
  {
    $this->userID = $data['userID'] ?? '';
    $this->memberID = $data['memberID'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->email = $data['email'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'userID' => $this->userID,
      'memberID' => $this->memberID,
      'name' => $this->name,
      'email' => $this->email,
    ];
  }
}
