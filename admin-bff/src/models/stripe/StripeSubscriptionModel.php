<?php

namespace App\models\stripe;

use Stripe\Subscription;

// see: https://docs.stripe.com/api/subscriptions/create?lang=php
class StripeSubscriptionModel
{
  private string $stripeSubscriptionID;
  private string $object;
  private string $collectionMethod;
  private string $currency;
  private string $customer;

  public function __construct(Subscription $data)
  {
    $this->stripeSubscriptionID = $data->id;
    $this->object = $data->object;
    $this->collectionMethod = $data->collection_method;
    $this->currency = $data->currency;
    $this->customer = $data->customer;
  }

  public function toArray(): array
  {
    return [
      'stripeSubscriptionID' => $this->stripeSubscriptionID,
      'object' => $this->object,
      'collectionMethod' => $this->collectionMethod,
      'currency' => $this->currency,
      'customer' => $this->customer,
    ];
  }

  public function getStripeSubscriptionID(): string
  {
    return $this->stripeSubscriptionID;
  }
}
