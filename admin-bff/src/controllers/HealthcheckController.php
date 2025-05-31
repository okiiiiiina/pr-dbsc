<?php

class HealthCheckController {
  public function healthcheck() {
    Response::success(['message' => 'Healthcheck passed']);
  }
}
