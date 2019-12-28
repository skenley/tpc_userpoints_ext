<?php

/**
 * @file
 * Provide views data for the Order module.
 */

/**
 * Implements hook_views_data().
 */
function tpc_userpoints_ext_views_data() {
  $data['views']['commerce_order_total_tpc'] = [
    'title' => t('Order total TPC'),
    'help' => t('Displays the order total field, requires an Order ID argument.'),
    'area' => [
      'id' => 'commerce_order_total_tpc',
    ],
  ];
  return $data;
}
