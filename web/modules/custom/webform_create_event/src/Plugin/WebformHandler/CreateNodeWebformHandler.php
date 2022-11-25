<?php

namespace Drupal\webform_create_event\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "create_a_node",
 *   label = @Translation("Create a node"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new node from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class CreateNodeWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'choose_node_type' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $node_types = $this->getNodeTypes();
    $form['choose_node_type'] = [
      '#type' => 'select',
      '#title' => t('Content type'),
      '#default_value' => $this->configuration['choose_node_type'],
      '#options' => $node_types,
    ];

    return $form;
  }

  /**
   * Retrieves node types.
   *
   * @return array
   *   The result array.
   */
  protected function getNodeTypes() {
    $node_types = NodeType::loadMultiple();
    $result = [];
    foreach ($node_types as $node_type) {
      $result[$node_type->id()] = $node_type->label();
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['choose_node_type'] = $values['choose_node_type'];

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    $values = $webform_submission->getData();
    $node_args = [
      'type' => $this->configuration['choose_node_type'],
      'langcode' => 'en',
      'created' => time(),
      'changed' => time(),
      'uid' => 1,
      'moderation_state' => 'published',
      'title' => $values['node_title'],
      'body' => [
        'value' => $values['node_body'],
        'format' => 'full_html',
      ],
    ];
    $node = Node::create($node_args);
    $node->save();
  }

}
