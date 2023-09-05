<?php

namespace Drupal\custom_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\custom_event\Event\IncidentReportEvent;

/**
 * Implements the form controller.
 */
class CustomEventForm extends FormBase {

  /**
   * Constructs a new UserLoginForm.
   *
   * @param EventDispatcherInterface $eventDispatcher
   *   The event dispatcher service.
   */
  public function __construct(protected EventDispatcherInterface $eventDispatcher) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('event_dispatcher'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['incident_type'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => $this->t('What type of incident do you want to report?'),
      '#options' => [
        'stolen_princess' => $this->t('Missing princess'),
        'cat' => $this->t('Cat stuck in tree'),
        'joker' => $this->t('Something involving the Joker'),
      ],
    ];

    $form['incident'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#title' => $this->t('Incident report'),
      '#description' => $this->t('Describe the incident in detail. This information will be passed along to all crime fighters.'),
      '#cols' => 60,
      '#rows' => 5,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'custom_event_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $type = $form_state->getValue('incident_type');
    $report = $form_state->getValue('incident');

    // When dispatching or triggering an event, start by constructing a new
    // event object. Then use the event dispatcher service to notify any event
    // subscribers.
    $event = new IncidentReportEvent($type, $report);

    // Dispatch an event by specifying which event, and providing an event
    // object that will be passed along to any subscribers.
    $this->eventDispatcher->dispatch($event, IncidentReportEvent::NEW_REPORT);
  }

}
