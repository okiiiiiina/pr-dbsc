<?php

namespace App\repositories;

use App\core\error\CustomException;
use App\models\SubscriptionModel;

use App\storage\JsonLoader;;

class SubscriptionRepository
{
  private string $storageFile;
  private JsonLoader $jsonLoader;

  public function __construct()
  {
    $this->storageFile = __DIR__ . '/../storage/subscription.json';
    $this->jsonLoader = new JsonLoader($this->storageFile);
  }

  public function create(
    SubscriptionModel $sub,
  ): void {
    $subscriptions = $this->jsonLoader->load();
    $subscriptions[$sub->getID()] = $sub->toArray();

    $result = file_put_contents(
      $this->storageFile,
      json_encode($subscriptions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    if ($result === false) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to write subscription data to storage file');
    }
  }
}
