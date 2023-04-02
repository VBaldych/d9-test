<?php

namespace Drupal\private_tempstore_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;
use Drupal\private_tempstore_example\Service\MyServices;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Target controller of the WithStoreForm.php .
 */
class SimpleController extends ControllerBase {

  /**
   * Tempstore service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $clientRequest;

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Custom service.
   *
   * @var \Drupal\private_tempstore_example\MyServices
   */
  private $myServices;

  /**
   * Inject services.
   */
  public function __construct(PrivateTempStoreFactory $tempStoreFactory, ClientInterface $clientRequest, MessengerInterface $messenger, MyServices $myServices) {
    $this->tempStoreFactory = $tempStoreFactory;
    $this->clientRequest = $clientRequest;
    $this->messenger = $messenger;
    $this->myServices = $myServices;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private'),
      $container->get('http_client'),
      $container->get('messenger'),
      $container->get('private_tempstore_example.myservices')
    );
  }

  /**
   * Target method of the the WithStoreForm.php.
   *
   * 1. Get the parameters from the tempstore for this user
   * 2. Delete the PrivateTempStore data from the database (not mandatory)
   * 3. Display a simple message with the data retrieved from the tempstore
   * 4. Get the items from the rss file in a renderable array
   * 5. Create a link back to the form
   * 6. Render the array.
   *
   * @return array
   *   An render array.
   */
  public function showRssItems() {
    $tempstore = $this->tempStoreFactory->get('private_tempstore_example');
    $params = $tempstore->get('params');
    $url = $params['url'];
    $items = $params['items'];

    $build[]['message'] = [
      '#type' => 'markup',
      '#markup' => t("Url: @url - Items: @items", ['@url' => $url, '@items' => $items]),
    ];

    if ($articles = $this->myServices->getItemFromRss($url, $items)) {
      $build[]['data_table'] = $this->myServices->buildTheRender($articles);
    }

    $build[]['back'] = [
      '#type' => 'link',
      '#title' => 'Back to the form',
      '#url' => URL::fromRoute('private_tempstore_example.with_store_form'),
    ];

    $build['#cache']['max-age'] = 0;

    return $build;
  }

}
