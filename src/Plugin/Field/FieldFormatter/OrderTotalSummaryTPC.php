<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Field\FieldFormatter;

use Drupal\commerce_order\Plugin\Field\FieldFormatter\OrderTotalSummary;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'commerce_order_total_summary' formatter.
 *
 * @FieldFormatter(
 *   id = "commerce_order_total_summary_tpc",
 *   label = @Translation("Order total summary TPC"),
 *   field_types = {
 *     "commerce_price",
 *   },
 * )
 */
class OrderTotalSummaryTPC extends OrderTotalSummary {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $items->getEntity();
    $elements = [];
    if (!$items->isEmpty()) {
      $elements[0] = [
        '#theme' => 'commerce_order_total_summary_tpc',
        '#order_entity' => $order,
        '#totals' => $this->orderTotalSummary->buildTotals($order),
      ];
    }
    
    return $elements;
  }

}
