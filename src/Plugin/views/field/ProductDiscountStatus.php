<?php

namespace Drupal\commerce_product_discount\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;
use Drupal\commerce_product_discount\Entity\ProductDiscount;

/**
 * A handler to provide a field which gives the product discount status.
 *
 * @ViewsField("product_discount_status")
 */
class ProductDiscountStatus extends FieldPluginBase {
  use UncacheableFieldHandlerTrait;

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
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

    $form[$this->options['id']]['#tree'] = TRUE;
    foreach ($this->view->result as $row_index => $row) {
      // Default is inactive.
      $product_discount_status = 'inactive';
      $product = $this->getEntity($row);
      $product_discount = \Drupal::entityQuery('commerce_product_discount')
        ->condition('product_id', $product->id())
        ->execute();

      if (!empty($product_discount)) {
        $product_discount = ProductDiscount::load(reset($product_discount));
        $is_enabled = $product_discount->getPromotion()->isEnabled();
        if ($is_enabled) {
          $product_discount_status = 'active';
        }
      }

      $options = [
        'active' => $this->t('Active'),
        'inactive' => $this->t('Inactive'),
      ];

      $form[$this->options['id']][$row_index] = [
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => $product_discount_status,
      ];
    }

  }

}
