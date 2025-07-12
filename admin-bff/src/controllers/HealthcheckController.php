<?php

namespace App\controllers;

use App\core\Response;

class HealthCheckController
{
  public function healthcheck()
  {
    error_log("🐳");
    Response::success(['message' => 'Healthcheck passed']);
  }
}
