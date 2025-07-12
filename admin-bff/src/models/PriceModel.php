<?php

namespace App\models;

class PriceModel
{
  private string $stripePriceId;
  private int $price;
  private string $currency; // 例: "円"
  private string $interval; // 例: "month", "year"

  public function __construct(array $data)
  {
    $this->stripePriceId = $data['stripePriceId'] ?? '';
    $this->price = (int)($data['price'] ?? 0);
    $this->currency = $data['currency'] ?? '';
    $this->interval = $data['interval'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'stripePriceId' => $this->stripePriceId,
      'price' => $this->price,
      'currency' => $this->currency,
      'interval' => $this->interval,
    ];
  }
}
