<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/UserModel.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

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
    $tokens = $this->auth0Repo->exchangeCodeForTokens($code);
    $userInfo = $this->auth0Repo->syncUserFromToken($tokens->access_token);

    $userInfo['role'] = 'owner';
    $userInfo['refreshToken'] = $tokens->refresh_token;
    // $userInfo['updatedAt'] = date('Y-m-d H:i:s');

    $user = new UserModel($userInfo);
    $this->userRepo->upsertUser($user->toArray());

    $exp = time() + 3600;
    $payload = ['sub' => $user->sub, 'exp' => $exp, 'aud' => $_ENV['JWT_AUDIENCE']];
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
  }

  /**
   * validAccessToken
   */
  public function validAccessToken(?string $token): array
  {
    if (!$token) {
      return ['valid' => false, 'status' => 401, 'message' => 'No session token'];
    }

    try {
      $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    } catch (ExpiredException $e) {
      return ['valid' => false, 'status' => 401, 'message' => 'Expired token'];
    } catch (Exception $e) {
      return ['valid' => false, 'status' => 401, 'message' => 'Invalid token'];
    }

    if (!isset($payload->aud) || $payload->aud !== $_ENV['JWT_AUDIENCE']) {
      return ['valid' => false, 'status' => 403, 'message' => 'Invalid audience'];
    }

    $user = $this->userRepo->findBySub($payload->sub ?? '');
    if (!$user) {
      return ['valid' => false, 'status' => 403, 'message' => 'User not found'];
    }

    return ['valid' => true, 'user' => $user];
  }
}
