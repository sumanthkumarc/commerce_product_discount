<?php

namespace Drupal\commerce_product_discount;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Product discount entity.
 *
 * @see \Drupal\commerce_product_discount\Entity\ProductDiscount.
 */
class ProductDiscountAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_product_discount\Entity\ProductDiscountInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished product discount entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published product discount entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit product discount entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete product discount entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add product discount entities');
  }

}
