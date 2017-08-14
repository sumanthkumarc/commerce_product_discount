<?php

namespace Drupal\commerce_product_discount\Plugin\views\field;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product_discount\Entity\ProductDiscount;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form element for saving the discount.
 *
 * @ViewsField("save_discount")
 */
class SaveDiscount extends FieldPluginBase {

  use UncacheableFieldHandlerTrait;

  /**
   * The Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new save discount button object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManager $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function clickSortable() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $row, $field = NULL) {
    return '<!--form-item-' . $this->options['id'] . '--' . $row->index . '-->';
  }

  /**
   * Form constructor for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    // Make sure we do not accidentally cache this form.
    $form['#cache']['max-age'] = 0;
    // The view is empty, abort.
    if (empty($this->view->result)) {
      unset($form['actions']);
      return;
    }

    $form['#tree'] = TRUE;
    $form[$this->options['id']]['#tree'] = TRUE;
    foreach ($this->view->result as $row_index => $row) {
      $product = $this->getEntity($row);

      $form[$this->options['id']][$row_index]['product_id'] = [
        '#type' => 'hidden',
        '#default_value' => $product->id(),
      ];

      $form[$this->options['id']][$row_index]['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save Discount'),
        '#row_index' => $row_index,
        '#attributes' => ['class' => ['discount-' . $row_index]],
        '#name' => 'discount-' . $row_index,
      ];

    }

    // Remove the form submit button.
    unset($form['actions']['submit']);
  }

  /**
   * Submit handler for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    $trig_el = $form_state->getTriggeringElement();
    $all_values = $form_state->getValues();

    $row_index = $trig_el['#row_index'];

    $product_discount_status = $all_values['product_discount_status'][$row_index];
    $product_discount_percent = $all_values['discount_amount'][$row_index];
    $product_id = $all_values['save_discount'][$row_index]['product_id'];

    $product_discount = ProductDiscount::available($product_id);

    if ($product_discount_status == 'active') {
      if (empty($product_discount)) {
        $product = Product::load($product_id);
        $order_types = $this->entityTypeManager->getStorage('commerce_order_type')
          ->loadMultiple();

        $store_ids = $product->getStoreIds();
        $order_types = array_keys($order_types);

        $promotion_id = ProductDiscount::createProductPromotion($product_id, $product_discount_percent, $store_ids, $order_types);

        $product_discount = ProductDiscount::create([
          'name' => 'product_discount_' . $product_id . '_' . $promotion_id,
          'product_id' => $product_id,
          'promotion_id' => $promotion_id,
        ]);

        $product_discount->save();
      }
      else {
        // Enable product promotion.
        $product_discount->setPublished(TRUE);
        $product_discount->syncPromotionAmount($product_discount_percent);
        $product_discount->enableProductPromotion();
      }
    }
    else {
      if (!empty($product_discount)) {
        // Disable the product promotion.
        $product_discount->disableProductPromotion();
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing.
  }

}
