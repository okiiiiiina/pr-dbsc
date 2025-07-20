<?php

namespace App\services;

require_once __DIR__ . '/../../vendor/autoload.php';

use Exception;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;


use App\core\error\CustomException;
use App\models\UserModel;
use App\repositories\AuthRepository;
use App\repositories\UserRepository;


class AuthService
{
  private AuthRepository $auth0Repo;
  private UserRepository $userRepo;

  public function __construct(AuthRepository $auth0Repo, UserRepository $userRepo)
  {
    $this->auth0Repo = $auth0Repo;
    $this->userRepo =  $userRepo;
  }

  /**
   * getGoogleSSOLink
   */
  public function getGoogleSSOLink(): string
  {
    return $this->auth0Repo->getGoogleSSOLink();
  }

  /**
   * loginWithCode
   */
  public function loginWithCode(string $code): array
  {
    try {
      $tokens = $this->auth0Repo->exchangeCodeForTokens($code);
      if (isset($tokens->error)) {
        throw new CustomException(401, 'Unauthorized', 'Failed to exchange code for tokens');
      }

      $userInfo = $this->auth0Repo->syncUserFromToken($tokens->access_token);
      if (isset($userInfo['error'])) {
        throw new CustomException(401, 'Unauthorized', 'Failed to retrieve user information from Auth0');
      }

      // 固定で owner
      $userInfo['role'] = 'owner';
      $userInfo['refreshToken'] = $tokens->refresh_token;

      $user = new UserModel($userInfo);
      $result = $this->userRepo->upsertUser($user->toArray());
      if (isset($result['error'])) {
        throw new CustomException(500, 'Internal Server Error', 'Failed to save user to storage.');
      }

      $exp = time() + 3600;
      $payload = ['sub' => $user->id, 'exp' => $exp, 'aud' => $_ENV['JWT_AUDIENCE']];
      $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

      $cookieOptions = [
        'expires' => $exp,
        'path' => '/',
        'domain' => $_ENV['COOKIE_DOMAIN'] ?? 'localhost',
        'secure' => true,
        'httponly' => true,
        'samesite' => $_ENV['COOKIE_SAMESITE'] ?? 'None'
      ];

      return ['user' => $user, 'token' => $jwt, 'cookieOptions' => $cookieOptions];
    } catch (\Throwable $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
      );
    }
  }

  /**
   * validAccessToken
   */
  public function validAccessToken(?string $token): string
  {
    if (!$token) {
      throw new CustomException(401, 'Unauthorized', 'No session token');
    }

    try {
      $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    } catch (ExpiredException $e) {
      throw new CustomException(401, 'Unauthorized', 'Expired token');
    } catch (Exception $e) {
      throw new CustomException(401, 'Unauthorized', 'Invalid token');
    }

    if (!isset($payload->aud) || $payload->aud !== $_ENV['JWT_AUDIENCE']) {
      throw new CustomException(403, 'Forbidden', 'Invalid audience');
    }

    if (empty($payload->sub)) {
      throw new CustomException(403, 'Forbidden', 'Missing subject (sub)');
    }

    return $payload->sub;
  }
}
