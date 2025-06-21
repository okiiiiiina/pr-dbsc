<?php

// require_once __DIR__ . '/WorkspacePlanModel.php';
// require_once __DIR__ . '/WorkspaceSubscriptionModel.php';
// require_once __DIR__ . '/WorkspacePriceModel.php';

class WorkspaceModel
{
  private string $id;
  private string $name;
  private string $stripeCustomerId;
  private DateTime $createdAt;
  private DateTime $updatedAt;
  private WorkspaceSubscriptionModel $subscription;
  private WorkspacePlanModel $plan;
  private WorkspacePriceModel $price;


  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? '';
    $this->name = $data['name'] ?? '';
    $this->stripeCustomerId = $data['stripe_customer_id'] ?? '';
    $this->createdAt = new DateTime($data['createdAt'] ?? 'now');
    $this->updatedAt = new DateTime($data['updatedAt'] ?? 'now');
    // subscription
    $this->subscription = isset($data['subscription']) && is_array($data['subscription'])
      ? new WorkspaceSubscriptionModel($data['subscription'])
      : new WorkspaceSubscriptionModel([]);
    // plan
    $this->plan = isset($data['plan']) && is_array($data['plan'])
      ? new WorkspacePlanModel($data['plan'])
      : new WorkspacePlanModel([]);
    // price
    $this->price = isset($data['price']) && is_array($data['price'])
      ? new WorkspacePriceModel($data['price'])
      : new WorkspacePriceModel([]);
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'stripeCustomerId' => $this->stripeCustomerId,
      'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
      'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
      'subscription' => $this->subscription,
      'plan' => $this->plan,
      'price' => $this->price,
    ];
  }
}

class WorkspaceSubscriptionModel
{
  private string $id;
  private string $stripeSubscriptionId;
  private ?DateTime $trialStartAt;
  private ?DateTime $trialEndAt;

  public function __construct(array $data)
  {
    $this->id = $data['id'] ?? '';
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
}

class WorkspacePlanModel
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


class WorkspacePriceModel
{
  private string $stripePriceId;
  private int $price;
  private string $currency;      // 例: "円"
  private string $interval;  // 例: "month", "year"

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
