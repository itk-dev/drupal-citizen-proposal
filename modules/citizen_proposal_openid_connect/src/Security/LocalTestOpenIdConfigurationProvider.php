<?php

namespace Drupal\citizen_proposal_openid_connect\Security;

use Drupal\citizen_proposal_openid_connect\Controller\OpenIDConnectController;
use Drupal\Core\Url;
use ItkDev\OpenIdConnect\Security\OpenIdConfigurationProvider;

/**
 *
 */
class LocalTestOpenIdConfigurationProvider extends OpenIdConfigurationProvider {

  public function __construct(array $options = [], array $collaborators = []) {
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthorizationUrl(array $options = []): string {
    return Url::fromRoute('citizen_proposal_openid_connect_local_test.openid_connect_authenticate', $options)
      ->toString(TRUE)->getGeneratedUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function getEndSessionUrl(
    string $postLogoutRedirectUri = NULL,
    string $state = NULL,
    string $idToken = NULL
  ): string {
    return Url::fromRoute('citizen_proposal_openid_connect_local_test.openid_connect_end_session', [
      OpenIDConnectController::QUERY_STRING_DESTINATION => $postLogoutRedirectUri,
    ])
      ->toString(TRUE)->getGeneratedUrl();
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
