<?php

namespace App\models;

// use DateTime;

class PlanModel
{
  private string $stripeProductId;
  private string $name;

  public function __construct(array $data)
  {
    $this->stripeProductId = $data['stripeProductId'] ?? '';
    $this->name = $data['name'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'stripeProductId' => $this->stripeProductId,
      'name' => $this->name,
    ];
  }
}
