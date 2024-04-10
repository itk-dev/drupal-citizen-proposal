<?php

namespace Drupal\citizen_proposal_openid_connect\Provider;

use Drupal\citizen_proposal_openid_connect\Controller\OpenIDConnectController;
use Drupal\Core\Url;
use ItkDev\OpenIdConnect\Security\OpenIdConfigurationProvider;

/**
 *
 */
class LocalTestProvider extends OpenIdConfigurationProvider {

  public function __construct(array $options = [], array $collaborators = []) {
  }

  /**
   *
   */
  public function getAuthorizationUrl(array $options = []): string {
    return Url::fromRoute('citizen_proposal_openid_connect.openid_connect_local_test', $options + [
      OpenIDConnectController::QUERY_STRING_DESTINATION => Url::fromRoute('<current>')->toString(TRUE)->getGeneratedUrl(),
      'test' => TRUE,
    ]
    )->toString(TRUE)->getGeneratedUrl();
  }

  /**
   *
   */
  public function getEndSessionUrl(
    string $postLogoutRedirectUri = NULL,
    string $state = NULL,
    string $idToken = NULL
  ): string {
    return Url::fromRoute('citizen_proposal_openid_connect.openid_connect_local_test.end_session', [
      'end-session' => TRUE,
      OpenIDConnectController::QUERY_STRING_DESTINATION => $postLogoutRedirectUri,
    ]
    )->toString(TRUE)->getGeneratedUrl();
  }

  /**
   *
   */
  public function getIdToken(string $code): string {
    return $code;
  }

  /**
   *
   */
  public function validateIdToken(string $idToken, string $nonce): object {
    return json_decode($idToken);
  }

}
