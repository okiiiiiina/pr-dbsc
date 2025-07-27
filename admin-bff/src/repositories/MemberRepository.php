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
  private string $workspaceStoragePath = __DIR__ . '/../storage/workspace.json';

  private JsonLoader $memberJsonLoader;
  private JsonLoader $userJsonLoader;
  private JsonLoader $workspaceJsonLoader;

  public function __construct()
  {
    $this->memberJsonLoader = new JsonLoader($this->memberStoragePath);
    $this->userJsonLoader = new JsonLoader($this->userStoragePath);
    $this->workspaceJsonLoader = new JsonLoader($this->workspaceStoragePath);
  }

  /**
   * findMeByUserID
   */
  public function findMeByUserID(string $id): array
  {

    try {
      // user
      $users = $this->userJsonLoader->load();
      $user = $users[$id];

      // member
      $members = $this->memberJsonLoader->load();
      $member = null;
      foreach ($members as $m) {
        if ($m['userID'] === $user['id']) {
          $member = $m;
          break;
        }
      }

      if ($member) {
        // workspace
        $workspaces = $this->workspaceJsonLoader->load();
        $workspace = null;
        foreach ($workspaces as $w) {
          if ($member['workspaceID'] === $w['id']) {
            $workspace = $w;
            break;
          }
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
        'workspace' =>  $workspace,
      ];

      return $me;
    } catch (\Throwable $e) {
      throw new CustomException(500, 'Internal Server Error', 'Failed to load user data from storage');
    }
  }

  /**
   * getAll
   */
  public function getAll(string $wsID)
  {
    $members = $this->memberJsonLoader->load();
    $filtered = array_filter($members, function ($m) use ($wsID) {
      return $m['workspaceID'] === $wsID;
    });
    $result = array_values($filtered);

    $users = $this->userJsonLoader->load();

    foreach ($result as $i => $m) {
      if (isset($users[$m['userID']])) {
        $user = $users[$m['userID']];
        $result[$i]['email'] = $user['email'];
        break;
      }
    }

    return $result;
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
