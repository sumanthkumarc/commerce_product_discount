<?php

namespace Drupal\commerce_product_discount\Entity;

use CommerceGuys\Intl\Calculator;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\commerce_promotion\Entity\Promotion;

/**
 * Defines the Product discount entity.
 *
 * @ingroup commerce_product_discount
 *
 * @ContentEntityType(
 *   id = "commerce_product_discount",
 *   label = @Translation("Product discount"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_product_discount\ProductDiscountListBuilder",
 *     "views_data" = "Drupal\commerce_product_discount\Entity\ProductDiscountViewsData",
 *     "translation" = "Drupal\commerce_product_discount\ProductDiscountTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\commerce_product_discount\Form\ProductDiscountForm",
 *       "add" = "Drupal\commerce_product_discount\Form\ProductDiscountForm",
 *       "edit" = "Drupal\commerce_product_discount\Form\ProductDiscountForm",
 *       "delete" = "Drupal\commerce_product_discount\Form\ProductDiscountDeleteForm",
 *     },
 *     "access" = "Drupal\commerce_product_discount\ProductDiscountAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_product_discount\ProductDiscountHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "commerce_product_discount",
 *   data_table = "commerce_product_discount_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer product discount entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/commerce_product_discount/{commerce_product_discount}",
 *     "add-form" = "/admin/structure/commerce_product_discount/add",
 *     "edit-form" = "/admin/structure/commerce_product_discount/{commerce_product_discount}/edit",
 *     "delete-form" = "/admin/structure/commerce_product_discount/{commerce_product_discount}/delete",
 *     "collection" = "/admin/structure/commerce_product_discount",
 *   },
 *   field_ui_base_route = "commerce_product_discount.settings"
 * )
 */
class ProductDiscount extends ContentEntityBase implements ProductDiscountInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Product discount entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Product discount entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['product_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Product'))
      ->setDescription(t('The product to which discount belongs to.'))
      ->setSetting('target_type', 'commerce_product')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['promotion_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Promotion'))
      ->setDescription(t('The promotion which is applied to this product.'))
      ->setSetting('target_type', 'commerce_promotion')
      ->setSetting('handler', 'default')
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Product discount is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getProductId() {
    return $this->get('product_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getProduct() {
    return $this->get('product_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getPromotionId() {
    return $this->get('promotion_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getPromotion() {
    return $this->get('promotion_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function createProductPromotion($product_id, $discount_percentage, array $store_ids, array $order_types) {

    if ($discount_percentage !== 0) {
      $discount_amount = Calculator::divide($discount_percentage, 100, 2);
    }
    else {
      $discount_amount = 0;
    }

    $promotion = Promotion::create([
      'name' => 'Product_' . $product_id . '_Promotion',
      'order_types' => $order_types,
      'stores' => $store_ids,
      'status' => TRUE,
      'offer' => [
        'target_plugin_id' => 'order_item_percentage_off',
        'target_plugin_configuration' => [
          'amount' => $discount_amount,
        ],
      ],
      'conditions' => [
        [
          'target_plugin_id' => 'order_item_product',
          'target_plugin_configuration' => [
            'products' => [
              [
                'product_id' => $product_id,
              ],
            ],
          ],
        ],
      ],
    ]);
    $promotion->save();

    return $promotion->id();
  }

  /**
   * @inheritdoc
   */
  public function enableProductPromotion() {
    /* @var Promotion $promotion */
    $promotion = $this->getPromotion();
    $promotion->setEnabled(TRUE);
    $promotion->save();
  }

  /**
   * @inheritdoc
   */
  public function disableProductPromotion() {
    /* @var Promotion $promotion */
    $promotion = $this->getPromotion();
    $promotion->setEnabled(FALSE);
    $promotion->save();
  }

  /**
   * @inheritdoc
   */
  public function syncPromotionAmount($percentageAmount) {
    $promotion = $this->getPromotion();
    /* @var \Drupal\commerce_promotion\Plugin\Commerce\PromotionOffer\OrderItemPercentageOff; $offer */
    $offer = $promotion->getOffer();
    $old_discount_amount = $offer->getAmount();

    $new_offer = $promotion->get('offer')->first()->getValue();

    if ($percentageAmount !== 0) {
      $new_amount = Calculator::divide($percentageAmount, 100, 2);
    }
    else {
      $new_amount = 0;
    }

    if ($old_discount_amount !== $new_amount) {
      $new_offer['target_plugin_configuration']['amount'] = $new_amount;
      $promotion->set('offer', $new_offer);
      $promotion->save();
    }
  }

}
