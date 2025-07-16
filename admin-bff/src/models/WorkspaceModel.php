<?php

namespace App\models;

use DateTime;

class WorkspaceModel
{
  private string $id;
  private string $name;
  private string $stripeCustomerId;
  // private DateTime $createdAt;
  // private DateTime $updatedAt;


  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? $this->generateNewID();
    $this->name = $data['name'] ?? '';
    $this->stripeCustomerId = $data['stripeCustomerId'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'stripeCustomerId' => $this->stripeCustomerId,
      // $this->createdAt = new DateTime($data['createdAt'] ?? 'now');
      // $this->updatedAt = new DateTime($data['updatedAt'] ?? 'now');
    ];
  }

  public function getID(): string
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  private function generateNewID(): string
  {
    $dt = new DateTime();
    $milliseconds = (int) ($dt->format('u') / 1000);
    return 'ws_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  }
}
