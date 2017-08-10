<?php

/**
 * @file
 * Contains commerce_product_discount\commerce_product_discount.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */

/**
 * Implements hook_views_data().
 */
function commerce_product_discount_views_data() {

  $data['views']['table']['group'] = t('Custom Global');
  $data['views']['table']['join'] = [
    // #global is a special flag which allows a table to appear all the time.
    '#global' => [],
  ];

  $data['views']['product_discount_status'] = [
    'title' => t('Product discount status'),
    'help' => t('Gives the discount status of the product.'),
    'field' => [
      'id' => 'product_discount_status',
    ],
  ];

  $data['views']['discount_amount'] = [
    'title' => t('Discount amount'),
    'help' => t('Provides field for entering discount percentage.'),
    'field' => [
      'id' => 'discount_amount',
    ],
  ];

  $data['views']['save_discount'] = [
    'title' => t('Save Discount'),
    'help' => t('Save Discount'),
    'field' => [
      'id' => 'save_discount',
    ],
  ];

  return $data;
}
