<?php

namespace Drupal\products_endpoint_json\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for products.
 *
 * @ingroup products_endpoint_json
 */
class ProductsEndpointJson extends ControllerBase {

  /**
   * Returns a list of products.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing product information.
   */
  public function getProductList() {
    // Your code to retrieve the product information goes here.
    $products = \Drupal::entityTypeManager()->getStorage('commerce_product')->loadMultiple();

    // Loop through the products and build the output array.
    foreach ($products as $product) {
      $output[] = array(
        'title' => $product->getTitle(),
        'price' => $product->getPrice(),
      );
    }

    return new JsonResponse($output);
  }

}