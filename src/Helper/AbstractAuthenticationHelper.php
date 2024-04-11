<?php

namespace Drupal\citizen_proposal\Helper;

use Drupal\Core\Url;

/**
 * Abstract authentication helper.
 */
abstract class AbstractAuthenticationHelper {

  /**
   * Get authenticate URL.
   */
  abstract public function getAuthenticateUrl(): Url;

  /**
   * Get end session URL.
   */
  abstract public function getEndSessionUrl(): Url;

  /**
   * Get user data.
   */
  abstract public function getUserData(): ?array;

}
