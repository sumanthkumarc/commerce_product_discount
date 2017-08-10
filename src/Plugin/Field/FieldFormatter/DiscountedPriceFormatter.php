<?php

namespace Drupal\commerce_product_discount\Plugin\Field\FieldFormatter;

use Drupal\commerce_price\Plugin\Field\FieldFormatter\PriceCalculatedFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\commerce_product_discount\Entity\ProductDiscount;
use Drupal\commerce\Context;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Language\LanguageInterface;

/**
 * Plugin implementation of the 'discounted_price_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "discounted_price_formatter",
 *   label = @Translation("Discounted price formatter"),
 *   field_types = {
 *     "commerce_price"
 *   }
 * )
 */
class DiscountedPriceFormatter extends PriceCalculatedFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $store = $this->storeContext->getStore();
    $context = new Context($this->currentUser, $store);
    $elements = [];
    /** @var \Drupal\commerce_price\Plugin\Field\FieldType\PriceItem $item */
    foreach ($items as $delta => $item) {
      /** @var \Drupal\commerce\PurchasableEntityInterface $purchasable_entity */
      $purchasable_entity = $items->getEntity();
      $resolved_price = $this->chainPriceResolver->resolve($purchasable_entity, 1, $context);
      $number = $resolved_price->getNumber();
      $currency = $this->currencyStorage->load($resolved_price->getCurrencyCode());
      $resolved_price_string = $this->numberFormatter->formatCurrency($number, $currency);

      $actual_price = $purchasable_entity->getPrice();
      $actual_number = $actual_price->getNumber();
      $actual_price_string = $this->numberFormatter->formatCurrency($actual_number, $currency);

      $markup = $actual_price_string;

      $product_discount = \Drupal::entityQuery('commerce_product_discount')
        ->condition('product_id', $purchasable_entity->getProductId())
        ->execute();

      if (!empty($product_discount)) {
        $product_discount = ProductDiscount::load(reset($product_discount));
        $promotion = $product_discount->getPromotion();

        if ($promotion->isEnabled()) {
          $percentoff_obj = $promotion->getOffer();
          $amount = $percentoff_obj->getAmount();
          $amount_percent = $amount * 100;
          $offer = $resolved_price->multiply($amount)->multiply('-1');
          $discount_price = $resolved_price->add($offer);
          $discount_number = $discount_price->getNumber();
          $discount_price_string = $this->numberFormatter->formatCurrency($discount_number, $currency);

          $markup = "<span class='discount-price'>" . $discount_price_string . "</span> <s class='actual-price'>" . $resolved_price_string . "</s> <span class='off-amount'>" . $amount_percent . "% off</span>";
        }
      }

      $elements[$delta] = [
        '#markup' => $markup,
        '#cache' => [
          'tags' => $purchasable_entity->getCacheTags(),
          'contexts' => Cache::mergeContexts($purchasable_entity->getCacheContexts(), [
            'languages:' . LanguageInterface::TYPE_INTERFACE,
            'country',
          ]),
        ],
      ];
    }

    return $elements;
  }

}
