<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\CreditCard;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\Entity\PaymentMethodInterface;
use Drupal\commerce_payment\Exception\HardDeclineException;
use Drupal\commerce_payment\PaymentMethodTypeManager;
use Drupal\commerce_payment\PaymentTypeManager;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OnsitePaymentGatewayBase;
use Drupal\commerce_price\Price;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * The onsite payment gateway for user points commerce payments.
 *
 * @CommercePaymentGateway(
 *   id = "onsite_tpc_userpoints",
 *   label = "TPC User Points (Onsite)",
 *   display_label = "TPC User Points",
 *   forms = { },
 *   payment_method_types = {"user_points"},
 *   credit_card_types = { },
 *   requires_billing_information = FALSE,
 * )
 */
class OnsiteUserPointsPaymentGateway extends OnsitePaymentGatewayBase {
  
  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, 
    $plugin_definition, EntityTypeManagerInterface $entity_type_manager, 
    PaymentTypeManager $payment_type_manager, 
    PaymentMethodTypeManager $payment_method_type_manager, 
    TimeInterface $time) {
    
    parent::__construct($configuration, $plugin_id, $plugin_definition,
      $entity_type_manager, $payment_type_manager, 
      $payment_method_type_manager, $time);

  }
  
  /**
   * {@inheritdoc}
   */
  public function createPayment(PaymentInterface $payment, $capture = TRUE) {
    
    ksm('I think this going to do something....');
    $this->assertPaymentState($payment, ['new']);
    $payment_method = $payment->getPaymentMethod();
    $this->assertPaymentMethod($payment_method);

    // Perform the create payment request here, throw an exception if it fails.
    // See \Drupal\commerce_payment\Exception for the available exceptions.
    // Remember to take into account $capture when performing the request.
    $amount = $payment->getAmount();
    $payment_method_token = $payment_method->getRemoteId();
    // The remote ID returned by the request.
    $remote_id = '123456';
    $next_state = 'completed';

    $payment->setState($next_state);
    $payment->setRemoteId($remote_id);
    $payment->save();
    ksm('This did something...');
  }
  
  /**
   * {@inheritdoc}
   */
  public function capturePayment(PaymentInterface $payment, Price $amount = NULL) {
    // No need for capture, NULL implementation.
    return FALSE;
  }
  
  /**
   * {@inheritdoc}
   */
  public function voidPayment(PaymentInterface $payment) {
    $this->assertPaymentState($payment, ['authorization']);
    // Perform the void request here, throw an exception if it fails.
    // See \Drupal\commerce_payment\Exception for the available exceptions.
    $remote_id = $payment->getRemoteId();

    $payment->setState('authorization_voided');
    $payment->save();
  }
  
  /**
   * {@inheritdoc}
   */
  public function refundPayment(PaymentInterface $payment, Price $amount = NULL) {
    // TO BE IMPLEMENTED
    return FALSE;
  }
  
  /**
   * {@inheritdoc}
   */
  public function createPaymentMethod(PaymentMethodInterface $payment_method, 
    array $payment_details = NULL) {
    // Not allowing the creation of additional pay methods for this gateway
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function deletePaymentMethod(PaymentMethodInterface $payment_method) {
    // Not allowing the deletion of pay methods for this gateway
    return FALSE;
  }
  
  /**
   * {@inheritdoc}
   */
  public function updatePaymentMethod(PaymentMethodInterface $payment_method) {
    // Not allowing the update of pay methods for this gateway
    return FALSE;
  }
  
}