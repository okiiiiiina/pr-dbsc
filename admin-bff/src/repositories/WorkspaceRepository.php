<?php

namespace App\repositories;

use App\models\WorkspaceModel;
use App\models\MemberModel;
use App\models\SubscriptionModel;
use App\models\PaymentInfoModel;

class WorkspaceRepository
{
  private string $storageFile = __DIR__ . '/../storage/users.json';

  /**
   * create
   */
  public function create(
    WorkspaceModel $ws,
    SubscriptionModel $sub,
    MemberModel $mem,
    PaymentInfoModel $pay
  ): void {
    // stripeを全部保存する


    // jsonに書き込む
    // ワークスペース作成
    $ws = [];
    if (file_exists($this->storageFile)) {
      $json = file_get_contents($this->storageFile);
      $ws = json_decode($json, true) ?? [];
    }

    // $ws[] = $data;

    // file_put_contents(
    //   $this->storageFile,
    //   json_encode($ws, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    // );
  }
}
