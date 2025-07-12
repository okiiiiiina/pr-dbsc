<?php

namespace App\controllers;

use App\core\Response;
use App\services\InviteService;

class InviteController
{
  protected InviteService $service;

  public function __construct(InviteService $service)
  {
    $this->service = $service;
  }

  public function handleInvite(): void
  {
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);

    if (!isset($data['email']) || $data['email'] === '' || isset($data['error'])) {
      Response::error('Missing authorization code', 400);
      return;
    }

    $this->service->invite($data['email']);
    // if ($res['error']) 的な感じか、throwしたら勝手にレスポンス変えるんだっけ

    Response::success([], 204);
  }
}
