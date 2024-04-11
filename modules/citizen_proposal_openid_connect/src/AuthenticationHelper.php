<?php

namespace Drupal\citizen_proposal_openid_connect;

use Drupal\citizen_proposal\Helper\AbstractAuthenticationHelper;
use Drupal\citizen_proposal_openid_connect\Controller\OpenIDConnectController;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Authentication helper.
 */
class AuthenticationHelper extends AbstractAuthenticationHelper {
  private const SESSION_USER_DATA = 'citizen_proposal_openid_connect_user_data';

  /**
   * Constructor.
   */
  public function __construct(
    readonly private SessionInterface $session
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthenticateUrl(): Url {
    return Url::fromRoute('citizen_proposal_openid_connect.openid_connect_authenticate', [
      OpenIDConnectController::QUERY_STRING_DESTINATION => Url::fromRoute('<current>')->toString(TRUE)->getGeneratedUrl(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getEndSessionUrl(): Url {
    return Url::fromRoute('citizen_proposal_openid_connect.openid_connect_end_session', [
      OpenIDConnectController::QUERY_STRING_DESTINATION => Url::fromRoute('<current>')->toString(TRUE)->getGeneratedUrl(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getUserData(): ?array {
    $data = $this->session->get(self::SESSION_USER_DATA);

    return $data ? (array) $data : NULL;
  }

  /**
   * Set user data.
   */
  public function setUserData(array $data) {
    $map = $this->getSettings()['openid_connect']['claims'] ?? [];
    foreach ($map as $expected => $actual) {
      if (isset($data[$actual])) {
        $data[$expected] = $data[$actual];
      }
    }

    $this->session->set(self::SESSION_USER_DATA, $data);
  }

  /**
   * Remove user data.
   */
  public function removeUserData(): void {
    $this->session->remove(self::SESSION_USER_DATA);
  }

  /**
   * Get this module's settings.
   *
   * @phpstan-return array<string, mixed>
   */
  public function getSettings(): array {
    $settings = Settings::get('citizen_proposal_openid_connect', NULL);

    return $settings ?: [];
  }

}
