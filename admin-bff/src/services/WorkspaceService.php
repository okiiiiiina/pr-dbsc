<?php

namespace App\services;

use App\core\AuthContext;
use App\dto\request\workspace\CreateRequest;
use App\repositories\WorkspaceRepository;
use App\models\WorkspaceModel;
use App\models\MemberModel;
use App\models\SubscriptionModel;
use App\models\PaymentInfoModel;

use App\core\Response;

use DateTime;
use DateInterval;

class WorkspaceService
{
  private WorkspaceRepository $repo;

  public function __construct(WorkspaceRepository $repo)
  {
    $this->repo = $repo;
  }

  public function create(CreateRequest $data): void
  {
    $ws = new WorkspaceModel($data->toArray());

    $user = AuthContext::getUser();

    $dt = new DateTime();
    $start = $dt->format('Y-m-d H:i:s');

    $dt_end = clone $dt;
    $dt_end->add(new DateInterval('P10D'));
    $end = $dt_end->format('Y-m-d H:i:s');

    $mem = new MemberModel([
      'userID' => $user['userID'],
      'name' => explode('@', $user['email'])[0],
      'email' => $user['email'],
    ]);
    $sub = new SubscriptionModel([
      'workspaceID' => $ws->getID(),
      'stripeSubscriptionId' => '',
      'trialStartAt' => $start,
      'trialEndAt' => $end,
    ]);
    $pay = new PaymentInfoModel([
      'workspaceID' => $ws->getID(),
      'billingEmail' => $ws->getName(),
      'billingName' => $ws->getName(),
    ]);

    Response::success([
      'status' => 'success',
      'ws' => $ws->toArray(),
      'mem' => $mem->toArray(),
    ], 200);

    // $this->repo->create($ws, $sub, $mem, $pay);
  }



  // public function getWorkspace(): array
  // {
  //   $rawWorkspaces = $this->repo->fetchAll();
  //   $workspaces = [];

  //   foreach ($rawWorkspaces as $workspaceData) {
  //     $workspaces[] = (new WorkspaceModel($workspaceData))->toArray();
  //   }

  //   return $workspaces;
  // }
}
