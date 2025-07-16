<?php

namespace App\models;

use DateTime;

class MemberModel
{
  private string $id;
  private string $name;
  private string $email;
  private string $workspaceID;
  private string $userID;


  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? $this->generateNewID();
    $this->name = $data['name'] ?? '';
    $this->email = $data['email'] ?? '';
    $this->workspaceID = $data['workspaceID'] ?? '';
    $this->userID = $data['userID'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'email' => $this->email,
      'name' => $this->name,
      'workspaceID' => $this->workspaceID,
      'userID' => $this->userID,
    ];
  }

  public function getID()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getWorkspaceID()
  {
    return $this->workspaceID;
  }

  public function getUserID()
  {
    return $this->userID;
  }

  private function generateNewID(): string
  {
    $dt = new DateTime();
    $milliseconds = (int) ($dt->format('u') / 1000);
    return 'mem_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  }
}
