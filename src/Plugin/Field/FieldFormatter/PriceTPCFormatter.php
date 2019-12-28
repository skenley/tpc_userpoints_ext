<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\commerce_price\Plugin\Field\FieldFormatter\PricePlainFormatter;

/**
 * Plugin implementation of the 'commerce_price_tpc' formatter.
 *
 * @FieldFormatter(
 *   id = "commerce_price_tpc",
 *   label = @Translation("Points"),
 *   field_types = {
 *     "commerce_price"
 *   }
 * )
 */
class PriceTPCFormatter extends PricePlainFormatter {
  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    // Load the parent method first as it does most of what we want
    $elements = parent::viewElements($items, $langcode);
    
    // Override the theme so that TPC prices are displayed with points
    // at the end instead of TUP (the currency code).
    foreach($elements as &$element) {
      
      $element['#theme'] = 'commerce_price_tpc';
      
    }
    
    return $elements;
  }
  
}