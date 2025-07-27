<?php

namespace App\libs;

use App\core\error\CustomException;
use App\models\stripe\StripeCustomerModel;
use App\models\stripe\StripeSubscriptionModel;

use Stripe\StripeClient;

class Stripe
{
  private $stripe;

  public function __construct()
  {
    $this->stripe = new StripeClient($_ENV['STRIPE_SECRET_API_KEY']);
  }

  /**
   * Customer
   */

  public function createCutomer(
    string $name,
    string $email,
    string $wsID,
  ): StripeCustomerModel {
    $res = $this->stripe->customers->create([
      'name' => $name,
      'email' => $email,
      'metadata' => [
        'workspace_id' => $wsID, // stripe スネークケースでいいのだろうか
      ],
    ]);

    $customer = new StripeCustomerModel($res);

    return $customer;
  }

  /**
   * Subscription
   */

  public function createSubscription(
    string $customerID,
    string $priceID,
    int $quantity,
    int $end,
    string $subID,
  ): StripeSubscriptionModel {
    $res = $this->stripe->subscriptions->create([
      'customer' => $customerID,
      'items' => [[
        'price' => $priceID,
        'quantity' => $quantity,
      ]],
      'trial_end' => $end,
      'metadata' => [
        'subscription_id' => $subID, // スネークケースでいいのだろうか
      ],
    ]);

    $sub = new StripeSubscriptionModel($res);

    return $sub;
  }
}
