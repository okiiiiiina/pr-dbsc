<?php

namespace App\core;

class JsonLoader
{
  private string $filePath;

  public function __construct(string $filePath)
  {
    $this->filePath = $filePath;
  }

  public function load(): array
  {
    if (!file_exists($this->filePath)) {
      return [];
    }

    $json = file_get_contents($this->filePath);
    return json_decode($json, true) ?? [];
  }
}
