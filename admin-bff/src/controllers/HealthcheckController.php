<?php

class HealthCheckController
{
  public function healthcheck()
  {
    error_log("🐳");
    Response::success(['message' => 'Healthcheck passed']);
  }
}
