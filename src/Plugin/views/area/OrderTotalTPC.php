<?php

namespace Drupal\tpc_userpoints_ext\Plugin\views\area;

use Drupal\commerce_order\Plugin\views\area\OrderTotal;
use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Defines an order total area handler.
 *
 * Shows the order total field with its components listed in the footer of a
 * View. Modified for TPC use.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("commerce_order_total_tpc")
 */
class OrderTotalTPC extends OrderTotal {

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE) {
    if (!$empty || !empty($this->options['empty'])) {
      foreach ($this->view->argument as $name => $argument) {
        // First look for an order_id argument.
        if (!$argument instanceof NumericArgument) {
          continue;
        }
        if (!in_array($argument->getField(), ['commerce_order.order_id', 'commerce_order_item.order_id'])) {
          continue;
        }
        if ($order = $this->orderStorage->load($argument->getValue())) {
          return $order->get('total_price')->view(['label' => 'hidden', 'type' => 'commerce_order_total_summary_tpc']);
        }
      }
    }

    return [];
  }

}
