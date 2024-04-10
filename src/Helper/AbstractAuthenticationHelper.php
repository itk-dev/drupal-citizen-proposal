<?php

namespace Drupal\citizen_proposal\Helper;

use Drupal\Core\Url;

/**
 *
 */
abstract class AbstractAuthenticationHelper {

  /**
   *
   */
  abstract public function getAuthenticateUrl(): Url;

  /**
   *
   */
  abstract public function getEndSessionUrl(): Url;

  /**
   *
   */
  abstract public function getUserData(): ?array;

}
