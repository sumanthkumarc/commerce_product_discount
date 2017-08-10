<?php

namespace Drupal\commerce_product_discount\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Product discount entities.
 */
class ProductDiscountViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
