<?php

namespace Drupal\commerce_product_discount\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Product discount entities.
 *
 * @ingroup commerce_product_discount
 */
interface ProductDiscountInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Product discount name.
   *
   * @return string
   *   Name of the Product discount.
   */
  public function getName();

  /**
   * Sets the Product discount name.
   *
   * @param string $name
   *   The Product discount name.
   *
   * @return \Drupal\commerce_product_discount\Entity\ProductDiscountInterface
   *   The called Product discount entity.
   */
  public function setName($name);

  /**
   * Gets the Product discount creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Product discount.
   */
  public function getCreatedTime();

  /**
   * Sets the Product discount creation timestamp.
   *
   * @param int $timestamp
   *   The Product discount creation timestamp.
   *
   * @return \Drupal\commerce_product_discount\Entity\ProductDiscountInterface
   *   The called Product discount entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Product discount published status indicator.
   *
   * Unpublished Product discount are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Product discount is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Product discount.
   *
   * @param bool $published
   *   TRUE to set this Product discount to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\commerce_product_discount\Entity\ProductDiscountInterface
   *   The called Product discount entity.
   */
  public function setPublished($published);

  /**
   * Gets the Product Id to which discount belongs to.
   *
   * @return int
   *   The Product Id.
   */
  public function getProductId();

  /**
   * Gets the Product to which discount belongs to.
   *
   * @return \Drupal\commerce_product\Entity\Product
   *   The Product Object.
   */
  public function getProduct();

  /**
   * Gets the Promotion Id which applies to the Product.
   *
   * @return int
   *   The Promotion Id.
   */
  public function getPromotionId();

  /**
   * Gets the Promotion which applies to the Product.
   *
   * @return \Drupal\commerce_promotion\Entity\Promotion
   *   The Promotion Object.
   */
  public function getPromotion();

  /**
   * Creates promotion for a product and returns created promotion id.
   *
   * @param int $product_id
   *   The product id.
   * @param int $discount_percentage
   *   The discount percentage to set for product.
   * @param array $store_ids
   *   Store ids for promotion.
   * @param array $order_types
   *   Order types for promotion.
   *
   * @return int
   *   The promotion id of newly generated promotion which applies to given product.
   */
  public static function createProductPromotion($product_id, $discount_percentage, array $store_ids, array $order_types);

  /**
   * Enables the Promotion which belongs to the product.
   */
  public function enableProductPromotion();

  /**
   * Disables the Promotion which belongs to the product.
   */
  public function disableProductPromotion();

  /**
   * Syncs the Promotion amount ie., checks if its same , else sets it to new value.
   *
   * @param int $percentageAmount
   *   The discount percentage entered.
   */
  public function syncPromotionAmount($percentageAmount);

}
