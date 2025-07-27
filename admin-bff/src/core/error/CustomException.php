<?php

namespace App\core\error;

class CustomException extends \Exception
{
  protected string $class;
  // protected string $httpStatusMessage; // $statusText

  public function __construct(
    ?int $code,
    ?string $message,
    ?string $class = 'unknown',
    ?string $stackTrace = 'unknown'
  ) {
    parent::__construct($message, $code);
    $this->class = $class;

    // ☠️ ログ出力
    error_log("\n☠️ ============ ☠️\n【CustomException】\n【CODE】 $code\n【MESSAGE】 $message\n【CLASS】 $class\n【STACK_TRACE】 $stackTrace\n☠️ ============ ☠️\n");
  }

  public function toArray(): array
  {
    return [
      'error' => true,
      'code' => $this->getCode(),
      'message' => $this->getMessage(),
      'class' => $this->class,
    ];
  }

  public static function defaultMessage(int $code): string
  {
    return match ($code) {
      400 => 'Bad Request',
      401 => 'Unauthorized',
      403 => 'Forbidden',
      404 => 'Not Found',
      422 => 'Unprocessable Entity',
      500 => 'Internal Server Error',
      default => 'Unknown Error',
    };
  }
}
