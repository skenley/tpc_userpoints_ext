<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\transaction\Entity\Transaction;
use Drupal\tpc_userpoints_ext\Entity\TOConfig;

/**
 * Action Description
 *
 * @Action(
 *   id = "tpc_userpoints_ext_transaction_action",
 *   label = "User Points Transaction Action",
 *   type = "",
 * )
 */
class UserPointsTransactionAction extends ViewsBulkOperationsActionBase {
  
  protected $transactionOpID;
  
  public function __construct(array $config, $pluginID, $pluginDef) {
    
    $this->transactionOpID = $_SESSION['toid'];
    parent::__construct($config, $pluginID, $pluginDef);
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    
    // Load the config associated with this transaction operation to 
    // get the point value associated with the transaction ID.
    $conf = TOConfig::load($this->transactionOpID);
    $newTran = Transaction::create([
      'type' => 'userpoints_default_points', 
      'operation' => $this->transactionOpID,
      'target_entity' => $entity,
      'field_userpoints_default_amount' => 
        strval($conf->getDefaultPointValue()),
    ]);

    $newTran->execute();
    // Saving each one during a bulk execution may bog down execution,
    // it should be considered to call the executeMultiple method in here,
    // and then in the executeMultiple save all the transactions afterward.
    $newTran->save();
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL,
    $returnAsObject = FALSE) {
    
    return TRUE;
    
  }
  
}