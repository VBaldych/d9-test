<?php

namespace Drupal\custom_drush_cmd\Commands;

use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * @package Drupal\custom_drush_cmd\Commands
 */
class BatchCommands extends DrushCommands {

  /**
   * A custom Drush command to displays the given text.
   *
   * @param string $text
   *   Argument with text to be printed.
   * @param array $options
   *   Options for command.
   *
   * @option uppercase Uppercase the text
   *
   * @command drush-command-example:print-me
   * @aliases ccepm,cce-print-me
   */
  public function printMe(string $text = 'Hello world!', array $options = ['uppercase' => FALSE]) {
    if ($options['uppercase']) {
      $text = strtoupper($text);
    }

    $this->output()->writeln($text);
  }

}
