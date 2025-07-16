<?php

namespace App\models;

use DateTime;

class PaymentInfoModel
{
  private string $id;
  private string $workspaceID;
  private string $billingEmail;
  private string $billingName;

  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? $this->generateNewID();
    $this->workspaceID = $data['workspaceID'] ?? '';
    $this->billingEmail = $data['billingEmail'] ?? '';
    $this->billingName = $data['billingName'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'workspaceID' => $this->workspaceID,
      'billingEmail' => $this->billingEmail,
      'billingName' => $this->billingName,
    ];
  }

  public function toStorageArray(): array
  {
    return [
      'id' => $this->id,
      'workspaceID' => $this->workspaceID,
      'billingEmail' => $this->billingEmail,
      'billingName' => $this->billingName,
    ];
  }

  public function getID()
  {
    return $this->id;
  }

  private function generateNewID(): string
  {
    $dt = new DateTime();
    $milliseconds = (int) ($dt->format('u') / 1000);
    return 'pay_' . $dt->format('Ymd_His') . sprintf('%03d', $milliseconds);
  }
}
