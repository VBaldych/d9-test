<?php

namespace Drupal\custom_login_event\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\custom_login_event\Event\UserLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserLoginSubscriber.
 *
 * @package Drupal\custom_login_event\EventSubscriber
 */
class UserLoginSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a new UserLoginSubscriber object.
   *
   * @param Connection $database
   *   The database connection service.
   * @param DateFormatterInterface $dateFormatter
   *   The date formatter.
   * @param Messenger $messenger
   *    The date formatter.
   */
  public function __construct(
    protected Connection $database,
    protected DateFormatterInterface $dateFormatter,
    protected Messenger $messenger
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      UserLoginEvent::EVENT_NAME => 'onUserLogin',
    ];
  }

  /**
   * Subscribe to the user login event dispatched.
   *
   * @param UserLoginEvent $event
   *   Our custom event object.
   */
  public function onUserLogin(UserLoginEvent $event): void {
    $account_created = $this->database->select('users_field_data', 'ud')
      ->fields('ud', ['created'])
      ->condition('ud.uid', $event->account->id())
      ->execute()
      ->fetchField();

    $this->messenger->addStatus(t('Welcome, your account was created on %created_date.', [
      '%created_date' => $this->dateFormatter->format($account_created, 'short'),
    ]));
  }

}
