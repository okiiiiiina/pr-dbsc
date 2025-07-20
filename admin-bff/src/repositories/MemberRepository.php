<?php

namespace App\repositories;

use App\core\error\CustomException;
use App\models\MeModel;
use App\models\MemberModel;

use App\core\JsonLoader;

class MemberRepository
{
  private string $memberStoragePath = __DIR__ . '/../storage/member.json';
  private string $userStoragePath = __DIR__ . '/../storage/user.json';

  private JsonLoader $memberJsonLoader;
  private JsonLoader $userJsonLoader;

  public function __construct()
  {
    $this->memberJsonLoader = new JsonLoader($this->memberStoragePath);
    $this->userJsonLoader = new JsonLoader($this->userStoragePath);
  }

  /**
   * findMeByUserID
   */
  public function findMeByUserID(string $id): array
  {
    $users = $this->userJsonLoader->load();
    $user = $users[$id];

    $members = $this->memberJsonLoader->load();
    $member = null;
    foreach ($members as $m) {
      if ($m['userID'] === $user['id']) {
        $member = $m;
        break;
      }
    }

    // dbだと普通にレコード返すだけだからモデルにせずに返す。でも保存の時はモデルでもらう。もしくはモデルにしたものを、配列の状態にしてもらうか。
    $me = [
      'userID' => $user['id'],
      'memberID' => $member['id'],
      'name' => $member['name'],
      'email' => $user['email'],
      'role' => $member['role'],
      'logoPath' => $user['logoPath'],
    ];

    return $me;
  }

  /**
   * getAll
   */
  public function getAll(string $wsID)
  {
    $members = $this->memberJsonLoader->load();

    $list = array_filter($members, function ($m) use ($wsID) {
      return $m['workspaceID'] === $wsID;
    });

    return $list;
  }

  /**
   * create
   */
  public function create(
    MemberModel $mem,
  ): void {
    $members = $this->memberJsonLoader->load();
    $members[$mem->getID()] = [
      'id' => $mem->getID(),
      'name' => $mem->getName(),
      'workspaceID' => $mem->getWorkspaceID(),
      'userID' => $mem->getUserID(),
    ];

    $result = file_put_contents(
      $this->memberStoragePath,
      json_encode($members, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    if ($result === false) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to write subscription data to storage file');
    }
  }
}
