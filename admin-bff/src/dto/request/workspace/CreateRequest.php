<?php

namespace App\dto\request\workspace;

class CreateRequest
{
  public string $name;
  public string $plan;

  public function __construct(array $req)
  {
    $this->name = $req['name'];
    $this->plan = $req['plan'];
  }

  public function toArray(): array
  {
    return [
      'name' => $this->name,
      'plan' => $this->plan,
    ];
  }
}
