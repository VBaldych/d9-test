<?php

namespace Drupal\custom_login_event\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\user\UserInterface;

/**
 * Event that is fired when a user logs in.
 */
class UserLoginEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'custom_login_event.user_login';

  /**
   * Constructs the object.
   *
   * @param UserInterface $account
   *   The account of the user logged in.
   */
  public function __construct(public UserInterface $account) {}

}
