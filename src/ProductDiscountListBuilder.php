<?php

namespace Drupal\commerce_product_discount;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Product discount entities.
 *
 * @ingroup commerce_product_discount
 */
class ProductDiscountListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Product discount ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_product_discount\Entity\ProductDiscount */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.commerce_product_discount.edit_form',
      ['commerce_product_discount' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
