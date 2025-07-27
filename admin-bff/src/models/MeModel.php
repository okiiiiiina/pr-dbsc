<?php

namespace App\models;

use App\models\WorkspaceModel;

class MeModel
{
  private string $userID;
  private ?string $memberID;
  private string $name;
  private string $email;
  private string $role;
  private string $logoPath;
  private ?WorkspaceModel $workspace;


  public function __construct(array $data, ?array $wsData = [])
  {
    $this->userID = $data['userID'] ?? '';
    $this->memberID = $data['memberID'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->email = $data['email'] ?? '';
    $this->role = $data['role'] ?? '';
    $this->logoPath = $data['logoPath'] ?? '';
    $this->workspace = $wsData ? new WorkspaceModel($wsData) : null;
  }

  public function toArray(): array
  {
    return [
      'userID' => $this->userID,
      'memberID' => $this->memberID,
      'name' => $this->name,
      'email' => $this->email,
      'role' => $this->role,
      'logoPath' => $this->logoPath,
      'workspace' => $this->workspace ? $this->workspace->toArray() : null,
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
