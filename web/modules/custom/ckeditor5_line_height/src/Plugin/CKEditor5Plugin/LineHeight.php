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
   * The default config name for line height options.
   *
   * @var string
   */
  const CONFIG_NAME = 'line_height_options';

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

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return static::DEFAULT_CONFIGURATION;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form[static::CONFIG_NAME] = [
      '#type' => 'textarea',
      '#title' => $this->t('Line Height Options'),
      '#default_value' => implode(' ', $this->configuration[static::CONFIG_NAME]),
      '#description' => $this->t(
        'A list of line height options separated with " ".
        Default options are ' . implode(' ', static::DEFAULT_CONFIGURATION[static::CONFIG_NAME]) . '<br>
        The maximal value should be less than 10<br>
        If you want to reset your options, just clean the text field and click "Save configuration"'
      ),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    // Match the config schema structure at
    // ckeditor5.plugin.ckeditor5_line_height_line_height.
    $options_string = $form_state->getValue(static::CONFIG_NAME);

    if ($options_string !== "") {
      $string_without_extra_spaces = preg_replace('/\s+/', ' ', $options_string);
      $options_array = explode(' ', trim($string_without_extra_spaces));

      // Remove item if value >= 10
      foreach ($options_array as $key => $value) {
        if ($value >= 10) {
          unset($options_array[$key]);
        }
      }

      $form_state->setValue(static::CONFIG_NAME, array_unique($options_array));
    }
    else {
      $form_state->setValue(static::CONFIG_NAME, static::DEFAULT_CONFIGURATION[static::CONFIG_NAME]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration[static::CONFIG_NAME] = $form_state->getValue(static::CONFIG_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    return [
      'lineHeight' => [
        'options' => $this->configuration[static::CONFIG_NAME],
      ],
    ];
  }

}
