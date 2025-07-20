<?php

namespace App\services;

use App\repositories\MemberRepository;
use App\models\MeModel;
use App\models\MemberModel;

class MemberService
{
  private MemberRepository $repo;

  public function __construct(MemberRepository $repo)
  {
    $this->repo = $repo;
  }

  /**
   * @return MeModel
   */
  public function getMe(string $id): object
  {
    $res = $this->repo->findMeByUserID($id);

    $me = new MeModel([
      'userID' => $res['userID'],
      'memberID' => $res['memberID'],
      'name' => $res['name'],
      'email' => $res['email'],
      'role' => $res['role'],
      'logoPath' => $res['logoPath'],
    ]);

    error_log("🍎 サービス:" . json_encode($me->toArray(), true));

    return $me;
  }
}
