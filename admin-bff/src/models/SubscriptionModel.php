<?php

namespace App\models;

use DateTime;

class SubscriptionModel
{
  private string $id;
  private ?string $stripeSubscriptionId;
  private ?DateTime $trialStartAt;
  private ?DateTime $trialEndAt;

  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? $this->generateNewID();
    $this->stripeSubscriptionId = $data['stripeSubscriptionId'] ?? '';
    $this->trialStartAt = isset($data['trialStartAt']) ? new DateTime($data['trialStartAt']) : null;
    $this->trialEndAt   = isset($data['trialEndAt'])   ? new DateTime($data['trialEndAt'])   : null;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'stripeSubscriptionId' => $this->stripeSubscriptionId,
      'trialStartAt' => $this->trialStartAt?->format('Y-m-d H:i:s'),
      'trialEndAt' => $this->trialEndAt?->format('Y-m-d H:i:s'),
    ];
  }

  public function setStripeSubscriptionId(string $val)
  {
    $this->stripeSubscriptionId = $val;
  }

  private function generateNewID(): string
  {
    $dt = new DateTime();
    $milliseconds = (int) ($dt->format('u') / 1000);
    return 'ws_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  }

  // private function setID()
  // {
  //   if ($this->id) return;
  //   $dt = new DateTime();
  //   $milliseconds = (int) ($dt->format('u') / 1000);
  //   $this->id = 'sub_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  // }
}
