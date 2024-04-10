<?php

namespace Drupal\citizen_proposal\Helper;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;

/**
 *
 */
class AuthenticationHelper extends AbstractAuthenticationHelper {

  public function __construct(
    private readonly MessengerInterface $messenger
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getAuthenticateUrl(): Url {
    $this->messenger->addError('No authentication helper defined. Consider installing the "citizen_proposal_openid_connect" module.');

    return Url::fromRoute('<front>');
  }

  /**
   * {@inheritdoc}
   */
  public function getEndSessionUrl(): Url {
    return Url::fromRoute('<front>');
  }

  /**
   * {@inheritdoc}
   */
  public function getUserData(): ?array {
    return NULL;
  }

}
