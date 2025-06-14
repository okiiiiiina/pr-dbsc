<?php

require_once __DIR__ . '/route.php';

class Router
{
  private array $routes = [];

  public function registerRoutes()
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $domain = $segments[1] ?? ''; // "auth" や "member" を取得

    $this->routes = getRoutesByDomain($domain);
  }

  public function dispatch()
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $key = $_SERVER['REQUEST_METHOD'] . $path;

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
