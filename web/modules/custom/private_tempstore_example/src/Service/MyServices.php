<?php

namespace Drupal\private_tempstore_example\Service;

use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Core\Url;
use GuzzleHttp\ClientInterface;

/**
 * Class MyServices.
 *
 * Custom service methods.
 *
 * @package Drupal\ex_form_values
 */
class MyServices {

  /**
   * Drupal\Core\Messenger\MessengerInterface definition.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $clientRequest;

  /**
   * Drupal\Core\StringTranslation\TranslationManager definition.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  private $translationManager;

  /**
   * MyServices constructor.
   */
  public function __construct(MessengerInterface $messenger, ClientInterface $clientRequest, TranslationManager $translationManager) {
    $this->messenger = $messenger;
    $this->clientRequest = $clientRequest;
    $this->translationManager = $translationManager;
  }

  /**
   * Get items from Rss file.
   *
   * @param string $url
   *   The url of the rss file.
   * @param int $items
   *   The number of items to retrieve.
   *
   * @return array|bool
   *   Return an array of objects with the following properties: title - body - url.
   */
  public function getItemFromRss($url, $items) {
    try {
      $response = $this->clientRequest->get($url, ['headers' => ['Accept' => 'text/plain']]);
      $data = (string) $response->getBody();
      if (empty($data)) {
        $this->messenger->addWarning($this->translationManager->translate('The file RSS file is empty.'));
        return FALSE;
      }
    }
    catch (\Exception $e) {
      $this->messenger->addWarning($this->translationManager->translate('Can\'t get the RSS file. '));
      return FALSE;
    }

    $xmlObject = @simplexml_load_string($data);
    if (!$xmlObject) {
      $this->messenger->addWarning($this->translationManager->translate('This file has no XML format'));
      return FALSE;
    }

    $content = [];
    $loopItem = 1;
    foreach ($xmlObject->children()->children() as $child) {
      if (!empty($child->title)) {
        $item = new \stdClass();
        $item->title = $child->title->__toString();
        $item->body = substr(strip_tags($child->description->__toString()), 0, 100) . '...';
        $item->body = preg_replace('/\s+/', ' ', $item->body);
        $item->url = $child->link->__toString();
        $content[] = $item;
        $loopItem++;
        if ($loopItem > $items) :
          break;
        endif;
      }
    }

    return $content;
  }

  /**
   * Generate a render array of type table with articles.
   *
   * @param array $articles
   *   Articles to render.
   *
   * @return array
   *   An render array.
   */
  public function buildTheRender(array $articles) {
    $header = ["#", "Title", "Description"];
    $output = [];
    $options = [
      'attributes' => ['target' => '_blank'],
    ];
    foreach ($articles as $key => $value) {
      $output[$key]['key'] = $key + 1;
      $output[$key]['Title'] = Link::fromTextAndUrl(t($value->title), Url::fromUri($value->url, $options));
      $output[$key]['Description'] = $value->body;
    }
    
    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $output,
      '#empty' => t('No Data'),
    ];
  }

}
