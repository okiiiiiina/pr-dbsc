<?php

namespace App\models;

use DateTime;

class MemberModel
{
  private string $id;
  private string $workspaceID;
  private string $userID;
  private string $name;
  private string $email;


  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? $this->generateNewID();
    $this->workspaceID = $data['workspaceID'] ?? '';
    $this->userID = $data['userID'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->email = $data['email'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'workspaceID' => $this->workspaceID,
      'userID' => $this->userID,
      'email' => $this->email,
      'name' => $this->name,
    ];
  }

  public function getID()
  {
    return $this->id;
  }

  public function getWorkspaceID()
  {
    return $this->workspaceID;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  private function generateNewID(): string
  {
    $dt = new DateTime();
    $milliseconds = (int) ($dt->format('u') / 1000);
    return 'mem_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  }
}
