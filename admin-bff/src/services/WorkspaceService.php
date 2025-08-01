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

use DateTime;
use DateInterval;
use Error;
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

  /**
   * getMyList
   * @return WorkspaceModel[]
   */
  public function getMyList(): array
  {
    $me = AuthContext::getMe();

    try {
      $myListRaw = $this->repo->getMyList($me->getUserID());
      $res = array_map(fn($item) => new WorkspaceModel($item), $myListRaw);

      return $res;
    } catch (Exception $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
        $e->getTraceAsString()
      );
    }
  }

  /**
   * switch
   */
  public function switch($id): string
  {
    error_log("🐳" . $id);

    try {
      $ws = $this->repo->findByID($id);

      if (!$ws['id']) {
        throw new CustomException(500, 'Internal Server Error', 'Failed to Find Workspace [at service].');
      }
      return $ws['id'];
    } catch (Exception $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
        $e->getTraceAsString()
      );
    }
  }

  /**
   * create
   */
  public function create(CreateRequest $data): string
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
      // ws
      $ws->setStripeCustomerId($customer->getStripeCustomerID());
      $this->repo->create($ws);
      $this->subRepo->create($sub);

      // member
      $mem = new MemberModel([
        'workspaceID' => $ws->getID(),
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

      return $ws->getID();
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
