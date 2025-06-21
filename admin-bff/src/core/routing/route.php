<?php

// controller
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../controllers/HealthCheckController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
// servise
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/UserService.php';
// repository
require_once __DIR__ . '/../../repositories/AuthRepository.php';
require_once __DIR__ . '/../../repositories/UserRepository.php';

function authRoutes(): array
{
  $userRepo = new UserRepository();
  $authRepo = new AuthRepository();
  $authService = new AuthService($authRepo, $userRepo);
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

function healthRoutes(): array
{
  $healthController = new HealthCheckController();

  return [
    'GET/api/health' => [
      'handler' => fn() => $healthController->healthcheck(),
      'middleware' => ['AuthMiddleware'],
    ],
  ];
}

function getRoutesByDomain(string $domain): array
{
  error_log("🍏getRoutesByDomain🍏" . $domain);
  return match ($domain) {
    'auth'   => authRoutes(),
    'users' => userRoutes(),
    'health' => healthRoutes(),
    default  => [],
  };
}
