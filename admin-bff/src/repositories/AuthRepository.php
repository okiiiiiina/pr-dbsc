<?php

require_once __DIR__ . '/../models/Auth0Config.php';

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Token;

class AuthRepository
{
  private Auth0 $auth0;
  private Auth0Config $config;

  public function __construct()
  {
    $this->config = new Auth0Config();
    $this->auth0 = new Auth0([
      'domain' => $this->config->getDomain(),
      'clientId' => $this->config->getClientId(),
      'clientSecret' => $this->config->getClientSecret(),
      'cookieSecret' => $this->config->getCookieSecret(),
    ]);
  }

  /**
   * getGoogleSSOLink
   */
  public function getGoogleSSOLink(): string
  {
    return "https://{$this->config->getDomain()}/authorize?" . http_build_query([
      'client_id' => $this->config->getClientId(),
      'response_type' => 'code',
      'redirect_uri' => $this->config->getRedirectUri(),
      'connection' => 'google-oauth2',
      'scope' => 'openid profile email offline_access',
    ]);
  }

  /**
   * exchangeCodeForTokens
   */
  public function exchangeCodeForTokens($code): object
  {
    $payload = [
      'grant_type' => 'authorization_code',
      'client_id' => $this->config->getClientId(),
      'client_secret' => $this->config->getClientSecret(),
      'code' => $code,
      'redirect_uri' => $this->config->getRedirectUri(),
      'audience' => 'https://localhost:8101/'
    ];

    $context = stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json",
        'content' => json_encode($payload),
        'ignore_errors' => true
      ]
    ]);

    $response = file_get_contents("https://{$this->config->getDomain()}/oauth/token", false, $context);
    if (!$response) throw new Exception("Failed to get token from Auth0");

    $data = json_decode($response);
    if (!isset($data->access_token)) throw new Exception("Token not found in response: $response");

    return $data;
  }

  /**
   * syncUserFromToken
   */
  public function syncUserFromToken(string $accessToken): array
  {
    $context = stream_context_create([
      'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$accessToken}",
        'ignore_errors' => true
      ]
    ]);

    $response = file_get_contents("https://{$this->config->getDomain()}/oauth/userinfo", false, $context);
    if (!$response) throw new Exception("Failed to get userInfo from Auth0");

    $user = json_decode($response, true);
    if (!isset($user['sub'])) throw new Exception("Invalid userinfo response");

    return $user;
  }

  /**
   * verifyIdToken
   */
  public function verifyIdToken(string $idToken)
  {
    try {
      $token = $this->auth0->decode(token: $idToken, tokenType: Token::TYPE_ID_TOKEN);
      return $token->toArray();
    } catch (InvalidTokenException $e) {
      throw new Exception("Invalid ID token: " . $e->getMessage());
    }
  }
}
