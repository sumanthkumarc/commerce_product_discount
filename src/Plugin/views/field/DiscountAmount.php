<?php

namespace Drupal\commerce_product_discount\Plugin\views\field;

use Drupal\commerce_product_discount\Entity\ProductDiscount;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;

/**
 * A handler to provide a number field to provide discount percentage.
 *
 * @ViewsField("discount_amount")
 */
class DiscountAmount extends FieldPluginBase {
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
      $product_discount_percent = 0;

      $product = $this->getEntity($row);
      $product_discount = \Drupal::entityQuery('commerce_product_discount')
        ->condition('product_id', $product->id())
        ->execute();

      if (!empty($product_discount)) {
        $product_discount = ProductDiscount::load(reset($product_discount));
        $product_discount_percent = $product_discount->getPromotion()
          ->getOffer()->getAmount();
      }

      $form[$this->options['id']][$row_index] = [
        '#type' => 'number',
        '#default_value' => $product_discount_percent * 100,
        '#size' => 3,
        '#min' => 0,
        '#max' => 100,
        '#step' => 1,
      ];
    }

  }

  /**
   * {@inheritdoc}
   */
  public function clickSortable() {
    return FALSE;
  }

}
