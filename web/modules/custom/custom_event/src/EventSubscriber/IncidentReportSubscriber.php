<?php

namespace Drupal\custom_event\EventSubscriber;

use Drupal\custom_event\Event\IncidentReportEvent;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribe to IncidentReportEvent::NEW_REPORT events and react to new reports.
 */
class IncidentReportSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  public function __construct(protected Messenger $messenger) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[IncidentReportEvent::NEW_REPORT][] = ['notifyMario'];
    $events[IncidentReportEvent::NEW_REPORT][] = ['notifyBatman', -100];
    $events[IncidentReportEvent::NEW_REPORT][] = ['notifyDefault', -255];

    return $events;
  }

  /**
   * If this incident is about a missing princess notify Mario.
   *
   * Per our configuration above, this method is called whenever the
   * IncidentEvents::NEW_REPORT event is dispatched. This method is where you
   * place any custom logic that you want to perform when the specific event is
   * triggered.
   *
   * These responder methods receive an event object as their argument. The
   * event object is usually, but not always, specific to the event being
   * triggered and contains data about application state and configuration
   * relative to what was happening when the event was triggered.
   *
   * For example, when responding to an event triggered by saving a
   * configuration change you'll get an event object that contains the relevant
   * configuration object.
   *
   * @param \Drupal\custom_event\Event\IncidentReportEvent $event
   *   The event object containing the incident report.
   */
  public function notifyMario(IncidentReportEvent $event): void {
    // You can use the event object to access information about the event passed
    // along by the event dispatcher.
    if ($event->getType() == 'stolen_princess') {
      $this->messenger->addStatus($this->t('Mario has been alerted. Thank you. This message was set by an
      event subscriber. See @method()', ['@method' => __METHOD__]));
      // Optionally use the event object to stop propagation.
      // If there are other subscribers that have not been called yet this will
      // cause them to be skipped.
      $event->stopPropagation();
    }
  }

  /**
   * Let Batman know about any events involving the Joker.
   *
   * @param IncidentReportEvent $event
   *   The event object containing the incident report.
   */
  public function notifyBatman(IncidentReportEvent $event): void {
    if ($event->getType() == 'joker') {
      $this->messenger->addStatus($this->t('Batman has been alerted. Thank you. This message was set by an
      event subscriber. See @method()', ['@method' => __METHOD__]));
      $event->stopPropagation();
    }
  }

  /**
   * Handle incidents not handled by the other handlers.
   *
   * @param IncidentReportEvent $event
   *   The event object containing the incident report.
   */
  public function notifyDefault(IncidentReportEvent $event): void {
    $this->messenger->addStatus($this->t('Thank you for reporting this incident. This message was set by an
    event subscriber. See @method()', ['@method' => __METHOD__]));
    $event->stopPropagation();
  }

}

