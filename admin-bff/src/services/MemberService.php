<?php

namespace App\services;

use App\core\error\CustomException;
use App\repositories\MemberRepository;
use App\models\MeModel;
use App\models\WorkspaceModel;
use App\models\MemberModel;

use Exception;

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

    error_log("🍎service|getMe" . json_encode($res, true));

    $me = new MeModel([
      'userID' => $res['userID'],
      'memberID' => $res['memberID'],
      'name' => $res['name'],
      'email' => $res['email'],
      'role' => $res['role'],
      'logoPath' => $res['logoPath'],
    ], $res['workspace']);

    return $me;
  }

  /**
   * @return MemberModel[]
   */
  public function getAll(string $wsID): array
  {
    try {
      $list = $this->repo->getAll($wsID);
      $members = [];

      foreach ($list as $item) {
        $members[] = new MemberModel($item);
      }

      error_log("🍎service|getAll" . json_encode($list, true));

      return $members;
    } catch (Exception $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
        $e->getTraceAsString()
      );
    }
  }
}
