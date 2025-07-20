<?php

namespace App\core;

use App\models\MeModel;

class AuthContext
{
  private static ?MeModel $me = null;

  public static function setMe(MeModel $me): void
  {
    self::$me = $me;
  }

  public static function getMe(): ?MeModel
  {
    return self::$me;
  }
}
