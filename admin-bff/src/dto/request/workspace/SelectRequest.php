<?php

namespace App\dto\request\workspace;

class SelectRequest
{
  public string $id;

  public function __construct(array $req)
  {
    $this->id = $req['id'];
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
    ];
  }
}
