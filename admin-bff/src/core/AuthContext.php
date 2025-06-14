<?php

class AuthContext
{
  private static ?array $user = null;

  public static function setUser(array $user): void
  {
    self::$user = $user;
  }

  public static function getUser(): ?array
  {
    return self::$user;
  }
}
