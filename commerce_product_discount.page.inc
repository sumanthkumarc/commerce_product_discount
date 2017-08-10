<?php

/**
 * @file
 * Contains commerce_product_discount.page.inc.
 *
 * Page callback for Product discount entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Product discount templates.
 *
 * Default template: commerce_product_discount.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_commerce_product_discount(array &$variables) {
  // Fetch ProductDiscount Entity Object.
  $commerce_product_discount = $variables['elements']['#commerce_product_discount'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
