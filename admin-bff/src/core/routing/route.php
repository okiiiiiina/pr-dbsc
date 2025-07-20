<?php

// controller
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

use App\controllers\HealthCheckController;
use App\controllers\AuthController;
use App\controllers\UserController;
use App\controllers\WorkspaceController;

// servise
use App\services\AuthService;
use App\services\UserService;
use App\services\WorkspaceService;

// repository
use App\repositories\AuthRepository;
use App\repositories\MemberRepository;
use App\repositories\PaymentInfoRepository;
use App\repositories\SubscriptionRepository;
use App\repositories\UserRepository;
use App\repositories\WorkspaceRepository;

// lib
use App\libs\Stripe;

/**
 *
 */
function authRoutes(): array
{
  $authRepo = new AuthRepository();
  $userRepo = new UserRepository();
  $memRepo = new MemberRepository();
  $authService = new AuthService($authRepo, $userRepo, $memRepo);
  $authController = new AuthController($authService);

  return [
    'GET/api/auth/google-sso' => [
      'handler' => fn() => $authController->handleGetGoogleSSOLink(),
      'middleware' => [],
    ],
    'POST/api/auth/callback' => [
      'handler' => fn() => $authController->handleLoginCallback(),
      'middleware' => [],
    ],
  ];
}

/**
 *
 */
function userRoutes(): array
{
  $userRepo = new UserRepository();
  $userService = new UserService($userRepo);
  $userController = new UserController($userService);

  return [
    'GET/api/users/me' => [
      'handler' => fn() => $userController->handleGetMe(),
      'middleware' => ['AuthMiddleware'],
    ],
    'GET/api/users' => [
      'handler' => fn() => $userController->handleGetUsers(),
      'middleware' => ['AuthMiddleware'],
    ],
  ];
}

/**
 *
 */
function workspaceRoutes(): array
{
  $stripe = new Stripe();
  $wsRepo = new WorkspaceRepository();
  $memRepo = new MemberRepository();
  $paymentInfo = new PaymentInfoRepository();
  $subRepo = new SubscriptionRepository();
  $wsService = new WorkspaceService($wsRepo, $memRepo, $paymentInfo, $subRepo, $stripe);
  $wsController = new WorkspaceController($wsService);

  return [
    'GET/api/workspaces/my-list' => [
      'handler' => fn() => $wsController->handleGetMyList(),
      'middleware' => ['AuthMiddleware'],
    ],
    'POST/api/workspaces/switch' => [
      'handler' => fn() => $wsController->handleSwitch(),
      'middleware' => ['AuthMiddleware'],
    ],
    'POST/api/workspaces' => [
      'handler' => fn() => $wsController->handleCreate(),
      'middleware' => ['AuthMiddleware'],
    ],
  ];
}

function healthRoutes(): array
{
  $healthController = new HealthCheckController();

  return [
    'GET/api/health' => [
      'handler' => fn() => $healthController->healthcheck(),
      'middleware' => ['AuthMiddleware'],
      'middleware' => [],
    ],
  ];
}

function getRoutesByDomain(string $domain): array
{
  error_log("🍏getRoutesByDomain🍏" . $domain);
  return match ($domain) {
    'auth'   => authRoutes(),
    'users' => userRoutes(),
    'workspaces' => workspaceRoutes(),
    'health' => healthRoutes(),
    default  => [],
  };
}
