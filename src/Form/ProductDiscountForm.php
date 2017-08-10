<?php

namespace Drupal\commerce_product_discount\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Product discount edit forms.
 *
 * @ingroup commerce_product_discount
 */
class ProductDiscountForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\commerce_product_discount\Entity\ProductDiscount */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Product discount.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Product discount.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.commerce_product_discount.canonical', ['commerce_product_discount' => $entity->id()]);
  }

}
