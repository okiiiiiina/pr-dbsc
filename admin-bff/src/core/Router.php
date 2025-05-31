<?php

require_once __DIR__ . '/../controllers/HealthCheckController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class Router
{
  private $routes = [];

  public function registerRoutes()
  {
    $this->routes = [
      'GET/health' => [
        'handler' => function () {
          (new HealthCheckController())->healthcheck();
        },
        'middleware' => [],
      ],
      'GET/api/auth/google-sso' => [
        'handler' => function () {
          (new AuthController())->handleGetGoogleSSOLink();
        },
        'middleware' => [],
      ],
      'POST/api/auth/callback' => [
        'handler' => function () {
          (new AuthController())->handleLoginCallback();
        },
        'middleware' => [],
      ],
    ];
  }

  public function dispatch()
  {
    $key = $_SERVER['REQUEST_METHOD'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (isset($this->routes[$key])) {
      $route = $this->routes[$key];

      foreach ($route['middleware'] as $middlewareClass) {
        if (is_callable([$middlewareClass, 'handle'])) {
          $middlewareClass::handle();
        }
      }

      $route['handler']();
    } else {
      http_response_code(404);
      echo 'Not Found';
    }
  }
}
