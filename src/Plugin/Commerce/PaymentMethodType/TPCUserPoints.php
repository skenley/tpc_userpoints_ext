<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Commerce\PaymentMethodType;

use Drupal\entity\BundleFieldDefinition;
use Drupal\commerce_payment\Entity\PaymentMethodInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentMethodType\PaymentMethodTypeBase;

/**
 * Provides the credit card payment method type.
 *
 * @CommercePaymentMethodType(
 *   id = "user_points",
 *   label = @Translation("User Points"),
 * )
 */
class TPCUserPoints extends PaymentMethodTypeBase {

  /**
   * {@inheritdoc}
   */
  public function buildLabel(PaymentMethodInterface $payment_method) {
    
    return $this->t('User points.');
    
  }

}
