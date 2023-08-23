<?php

declare(strict_types=1);

namespace Drupal\ckeditor5_line_height\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Line Height plugin configuration.
 *
 */
class LineHeight extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * The default array of line height options.
   *
   * @var string[][]
   */
  const DEFAULT_CONFIGURATION = [
    'line_height_options' => [
      '0',
      '0.5',
      '1',
      '1.5',
      '2',
      '2.5',
      '3',
      '3.5',
      '4',
      '4.5',
      '5',
      '5.5',
      '6',
      '6.5',
    ],
  ];

//  @todo DEFAULT_CONFIGURATION in descr
//  https://www.drupal.org/project/console/issues/3337542 - drupal console
//

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return static::DEFAULT_CONFIGURATION;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['line_height_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Line Height Options'),
      '#default_value' => implode(' ', $this->configuration['line_height_options']),
      '#description' => $this->t('A list of line height options separated with " ".
                                        Default options are 0 0.5 1 1.5 2 2.5 3 3.5 4 4.5 5 5.5 6 6.5<br>
                                        If you want to reset your options, just clean the text field and click "Save configuration"'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Match the config schema structure at
    // ckeditor5.plugin.ckeditor5_line_height_line_height.
    $options_string = $form_state->getValue('line_height_options');

    if ($options_string !== "") {
      // @todo Try to change with HTMLRestrictions::fromString($options_string)->toCKEditor5ElementsArray();
      $options_array = explode(' ', $options_string);
      $form_state->setValue('line_height_options', $options_array);
    }
    else {
      $form_state->setValue('line_height_options', static::DEFAULT_CONFIGURATION['line_height_options']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['line_height_options'] = $form_state->getValue('line_height_options');
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    return [
      'lineHeight' => [
        'options' => $this->getConfiguration()['line_height_options'],
      ],
    ];
  }

}
