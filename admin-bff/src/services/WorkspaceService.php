<?php

namespace App\services;

use App\core\AuthContext;
use App\core\error\CustomException;
use App\dto\request\workspace\CreateRequest;
use App\libs\Stripe;
use App\models\MemberModel;
use App\models\PaymentInfoModel;
use App\models\SubscriptionModel;
use App\models\WorkspaceModel;
use App\repositories\MemberRepository;
use App\repositories\PaymentInfoRepository;
use App\repositories\SubscriptionRepository;
use App\repositories\WorkspaceRepository;

use App\core\Response;

use DateTime;
use DateInterval;
use Exception;

class WorkspaceService
{
  private WorkspaceRepository $repo;
  private MemberRepository $memRepo;
  private PaymentInfoRepository $payInfoRepo;
  private SubscriptionRepository $subRepo;
  private Stripe $stripe;

  public function __construct(
    WorkspaceRepository $repo,
    MemberRepository $memRepo,
    PaymentInfoRepository $payInfoRepo,
    SubscriptionRepository $subRepo,
    Stripe $stripe
  ) {
    $this->repo = $repo;
    $this->memRepo = $memRepo;
    $this->payInfoRepo = $payInfoRepo;
    $this->subRepo = $subRepo;
    $this->stripe = $stripe;
  }

  public function create(CreateRequest $data): void
  {
    $me = AuthContext::getMe();

    try {
      // stripe
      // customer(ws)
      $ws = new WorkspaceModel([
        'name' => $data->name,
        'email' => $me->getEmail(),
      ]);
      $customer = $this->stripe->createCutomer($ws->getName(), $me->getEmail(), $ws->getID());

      // subscription
      $dt = new DateTime();
      $start = $dt->format('Y-m-d H:i:s');

      $dtEnd = clone $dt;
      $dtEnd->add(new DateInterval('P10D'));
      $end = $dtEnd->format('Y-m-d H:i:s');

      $sub = new SubscriptionModel([
        'workspaceID' => $ws->getID(),
        'stripeSubscriptionId' => '',
        'trialStartAt' => $start,
        'trialEndAt' => $end,
      ]);
      $subscription = $this->stripe->createSubscription($customer->getStripeCustomerID(), 'price_1ROzX8HU2cWKbU6XFBMTm9LP', 1, $dtEnd->getTimestamp(), $sub->getID());
      $sub->setStripeSubscriptionId($subscription->getStripeSubscriptionID());

      // db
      // ws, subscription
      $this->repo->create($ws);
      $this->subRepo->create($sub);

      // member
      $mem = new MemberModel([
        'wsID' => $ws->getID(),
        'userID' => $me->getUserID(),
        'name' => explode('@', $me->getEmail())[0],
        'email' => $me->getEmail(),
      ]);
      $this->memRepo->create($mem);

      // paymentinfo
      $payInfo = new PaymentInfoModel([
        'workspaceID' => $ws->getID(),
        'billingName' => $ws->getName(),
        'billingEmail' => $mem->getEmail(),
      ]);
      $this->payInfoRepo->create($payInfo);
    } catch (Exception $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
        $e->getTraceAsString()
      );
    }
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
