<?php

namespace Drupal\hook_event\EventSubscriber;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityViewSubscriber implements EventSubscriberInterface {

  /**
   * Alter entity view.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent $event
   *   The event.
   */
  public function entityViewCallback(EntityViewEvent $event): void {
    $entity = $event->getEntity();

    // Only do this for entities of type Node.
    if ($entity instanceof NodeInterface) {
      $build = &$event->getBuild();
      $build['new_element'] = [
        '#type' => 'markup',
        '#markup' => '<i>Changed entity</i>',
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      EntityHookEvents::ENTITY_VIEW => 'entityViewCallback',
    ];
  }

}
