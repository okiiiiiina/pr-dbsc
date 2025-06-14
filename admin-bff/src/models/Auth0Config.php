<?php

class Auth0Config
{
  private string $domain;
  private string $clientId;
  private string $clientSecret;
  private string $redirectUri;
  private string $cookieSecret;

  public function __construct()
  {
    $this->domain = $_ENV['AUTH0_DOMAIN'];
    $this->clientId = $_ENV['AUTH0_CLIENT_ID'];
    $this->clientSecret = $_ENV['AUTH0_CLIENT_SECRET'];
    $this->redirectUri = $_ENV['AUTH0_REDIRECT_URI'];
    $this->cookieSecret = $_ENV['AUTH0_COOKIE_SECRET'];
  }

  public function getDomain(): string
  {
    return $this->domain;
  }
  public function getClientId(): string
  {
    return $this->clientId;
  }
  public function getClientSecret(): string
  {
    return $this->clientSecret;
  }
  public function getRedirectUri(): string
  {
    return $this->redirectUri;
  }
  public function getCookieSecret(): string
  {
    return $this->cookieSecret;
  }
}
