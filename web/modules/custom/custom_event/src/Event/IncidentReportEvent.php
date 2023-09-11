<?php

namespace Drupal\custom_event\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Wraps a incident report event for event subscribers.
 */
class IncidentReportEvent extends Event {

  /**
   * Name of the event fired when a new incident is reported.
   *
   * @var string
   */
  const NEW_REPORT = 'custom_event.new_incident_report';

  /**
   * Constructs an incident report event object.
   *
   * @param string $type
   *   The incident report type.
   * @param string $report
   *   A detailed description of the incident provided by the reporter.
   */
  public function __construct(protected string $type, protected string $report) {}

  /**
   * Get the incident type.
   *
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Get the detailed incident report.
   *
   * @return string
   */
  public function getReport(): string {
    return $this->report;
  }

}
