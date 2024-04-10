<?php

namespace Drupal\citizen_proposal_openid_connect\Controller;

use Drupal\citizen_proposal_openid_connect\AuthenticationHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class LocalTestController extends ControllerBase {

  public function __construct(
    readonly private AuthenticationHelper $helper,
  ) {}

  /**
   *
   */
  public function index(Request $request): array {
    $users = $this->getLocalTestUsers();

    return [
      '#theme' => 'citizen_proposal_openid_connect_local_test_users',
      '#users' => $users,
      '#query' => $request->query->all(),
    ];
  }

  /**
   *
   */
  public function endSession(Request $request): Response {
    $this->helper->removeUserData();
    $postLogoutRedirectUri = $this->getPostLogoutRedirectUri($request);

    return new TrustedRedirectResponse($postLogoutRedirectUri);
  }

  /**
   * Get post logout redirect uri.
   */
  private function getPostLogoutRedirectUri(Request $request): string {
    try {
      $url = $request->get(OpenIDConnectController::QUERY_STRING_DESTINATION) ?? '/';

      return Url::fromUserInput($url, [
        'path_processing' => FALSE,
      ])
        ->setAbsolute()
        ->toString(TRUE)->getGeneratedUrl();
    }
    catch (\Exception) {
      // Fallback if all other things fail.
      return '/';
    }
  }

  /**
   * Get local test users.
   *
   * @phpstan-return array<string, mixed>
   */
  private function getLocalTestUsers(): array {
    return (array) ($this->getSettings()['local_test_users'] ?? []);
  }

  /**
   * Get this module's settings.
   *
   * @phpstan-return array<string, mixed>
   */
  private function getSettings(): array {
    $settings = Settings::get('citizen_proposal_openid_connect', NULL);

    return $settings ?: [];
  }

}
