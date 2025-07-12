<?php

namespace App\models;

use DateTime;

class MemberModel
{
  private string $id;
  private string $userID;
  private string $name;
  private string $email;


  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? $this->generateNewID();
    $this->userID = $data['userID'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->email = $data['email'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'userID' => $this->userID,
      'name' => $this->name,
      'email' => $this->email,
    ];
  }


  private function generateNewID(): string
  {
    $dt = new DateTime();
    $milliseconds = (int) ($dt->format('u') / 1000);
    return 'mem_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  }

  // private function setID()
  // {
  //   if ($this->id) return;
  //   $dt = new DateTime();
  //   $milliseconds = (int) ($dt->format('u') / 1000);
  //   $this->id = 'mem_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  // }
}
