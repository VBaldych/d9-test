<?php

namespace Drupal\custom_event\Event;

// Note: For Drupal 8.x and 9.0.x use Symfony\Component\EventDispatcher\Event.
// The class Drupal\Component\EventDispatcher\Event was introduced in Drupal
// 9.1.x as a backwards compatibility layer to allow more easily upgrading to
// Symfony 5 for Drupal 10.
// @link https://www.drupal.org/node/3159012
#use Symfony\Component\EventDispatcher\Event;
use Drupal\Component\EventDispatcher\Event;

/**
 * Wraps a incident report event for event subscribers.
 *
 * Whenever there is additional contextual data that you want to provide to the
 * event subscribers when dispatching an event you should create a new class
 * that extends \Symfony\Component\EventDispatcher\Event.
 *
 * See \Drupal\Core\Config\ConfigCrudEvent for an example of this in core.
 *
 * @ingroup events_example
 */
class IncidentReportEvent extends Event {

  /**
   * Incident type.
   *
   * @var string
   */
  protected string $type;

  /**
   * Detailed incident report.
   *
   * @var string
   */
  protected string $report;

  /**
   * Constructs an incident report event object.
   *
   * @param string $type
   *   The incident report type.
   * @param string $report
   *   A detailed description of the incident provided by the reporter.
   */
  public function __construct(string $type, string $report) {
    $this->type = $type;
    $this->report = $report;
  }

  /**
   * Get the incident type.
   *
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Get the detailed incident report.
   *
   * @return string
   */
  public function getReport() {
    return $this->report;
  }

}
