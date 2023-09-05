<?php

namespace Drupal\custom_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\custom_event\Event\IncidentEvents;
use Drupal\custom_event\Event\IncidentReportEvent;

/**
 * Implements the SimpleForm form controller.
 *
 * The submitForm() method of this class demonstrates using the event dispatcher
 * service to dispatch an event.
 *
 * @see \Drupal\custom_event\Event\IncidentEvents
 * @see \Drupal\custom_event\Event\IncidentReportEvent
 * @see \Symfony\Component\EventDispatcher\EventDispatcherInterface
 * @see \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
 *
 * @ingroup events_example
 */
class EventsExampleForm extends FormBase {

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $event_dispatcher;

  /**
   * Constructs a new UserLoginForm.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service.
   */
  public function __construct(EventDispatcherInterface $event_dispatcher) {
    // The event dispatcher service is an implementation of
    // \Symfony\Component\EventDispatcher\EventDispatcherInterface. In Drupal
    // this is generally an instance of the
    // \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher service.
    // This dispatcher improves performance when dispatching events by compiling
    // a list of subscribers into the service container so that they do not need
    // to be looked up every time.
    $this->event_dispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['incident_type'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => t('What type of incident do you want to report?'),
      '#options' => [
        'stolen_princess' => $this->t('Missing princess'),
        'cat' => $this->t('Cat stuck in tree'),
        'joker' => $this->t('Something involving the Joker'),
      ],
    ];

    $form['incident'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#title' => t('Incident report'),
      '#description' => t('Describe the incident in detail. This information will be passed along to all crime fighters.'),
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
  public function getFormId() {
    return 'events_example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $type = $form_state->getValue('incident_type');
    $report = $form_state->getValue('incident');

    // When dispatching or triggering an event, start by constructing a new
    // event object. Then use the event dispatcher service to notify any event
    // subscribers.
    $event = new IncidentReportEvent($type, $report);

    // Dispatch an event by specifying which event, and providing an event
    // object that will be passed along to any subscribers.

    // As of Drupal 9.1.x, the argument order has switched.
    // @see https://www.drupal.org/node/3159012
    $this->event_dispatcher->dispatch($event, IncidentEvents::NEW_REPORT);
  }

}
