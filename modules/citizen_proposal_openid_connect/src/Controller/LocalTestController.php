<?php

namespace Drupal\citizen_proposal_openid_connect\Controller;

use Drupal\citizen_proposal_openid_connect\AuthenticationHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used for OpenID Connect authentication.
 */
class LocalTestController extends ControllerBase {

  /**
   * Constructor.
   */
  public function __construct(
    readonly private AuthenticationHelper $helper
  ) {}

  /**
   *
   */
  public function main(Request $request): array {
    $users = $this->helper->getSettings()['local_test_users'] ?? [];

    return [
      '#theme' => 'citizen_proposal_openid_connect_local_test_users',
      '#users' => $users,
      '#query' => $request->query->all(),
    ];
  }

  /**
   * End OpenID Connect session.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   */
  public function endSession(Request $request): Response {
    $this->helper->removeUserData();

    $endSessionUrl = $request->query->get(OpenIDConnectController::QUERY_STRING_DESTINATION, '/');

    return new TrustedRedirectResponse($endSessionUrl);
  }

}
