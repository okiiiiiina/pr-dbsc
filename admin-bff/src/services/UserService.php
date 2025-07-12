<?php

namespace App\services;

use App\repositories\UserRepository;
use App\models\UserModel;

class UserService
{
  private UserRepository $repo;

  public function __construct(UserRepository $repo)
  {
    $this->repo = $repo;
  }

  public function getAllUsers(): array
  {
    $rawUsers = $this->repo->fetchAll();
    $users = [];

    foreach ($rawUsers as $userData) {
      $users[] = (new UserModel($userData))->toArray();
    }

    return $users;
  }
}
