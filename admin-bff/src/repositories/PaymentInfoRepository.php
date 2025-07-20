<?php

namespace App\repositories;

use App\core\error\CustomException;
use App\models\PaymentInfoModel;

use App\core\JsonLoader;;

class PaymentInfoRepository
{
  private string $storageFile;
  private JsonLoader $jsonLoader;

  public function __construct()
  {
    $this->storageFile = __DIR__ . '/../storage/paymentInfo.json';
    $this->jsonLoader = new JsonLoader($this->storageFile);
  }

  public function create(
    PaymentInfoModel $pay,
  ): void {
    $pays = $this->jsonLoader->load();
    $pays[$pay->getID()] = $pay->toStorageArray();

    $result = file_put_contents(
      $this->storageFile,
      json_encode($pays, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    if ($result === false) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to write paymentInfo data to storage file');
    }
  }
}
