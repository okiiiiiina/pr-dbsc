<?php

namespace App\models\stripe;

use Stripe\Customer;

// see: https: //docs.stripe.com/api/customers/create?lang=php
class StripeCustomerModel
{
  private string $stripeCustomerID;
  private string $object;
  private string $balance;
  private string $email;
  private string $name;
  private string $metadata;

  public function __construct(Customer $data)
  {
    $this->stripeCustomerID = $data->id;
    $this->object = $data->object;
    $this->balance = $data->balance;
    $this->email = $data->email;
    $this->name = $data->name;
    $this->metadata = $data->metadata;
  }

  public function toArray(): array
  {
    return [
      'stripeCustomerID' => $this->stripeCustomerID,
      'object' => $this->object,
      'balance' => $this->balance,
      'email' => $this->email,
      'name' => $this->name,
      'metadata' => $this->metadata,
    ];
  }

  public function getStripeCustomerID(): string
  {
    return $this->stripeCustomerID;
  }
}
