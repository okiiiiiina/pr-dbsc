<?php
class DbscKeyStore
{
  private static string $file = __DIR__ . '/../storage/dbsc_keys.json';

  private static function load(): array
  {
    if (!file_exists(self::$file)) return [];
    $json = file_get_contents(self::$file);
    return json_decode($json, true) ?? [];
  }

  private static function save(array $data): void
  {
    file_put_contents(self::$file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
  }

  public static function put(string $id, string $pubKey): void
  {
    $data = self::load();
    $data[$id] = $pubKey;
    self::save($data);
  }

  public static function get(string $id): ?string
  {
    $data = self::load();
    return $data[$id] ?? null;
  }
}
