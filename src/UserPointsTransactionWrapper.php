<?php

namespace Drupal\tpc_userpoints_ext;

use Drupal\transaction\Entity\Transaction;
use Drupal\user\Entity\User;

class UserPointsTransactionWrapper {
  
  /**
   * @var Drupal\transaction\Entity\Transaction
   */
  private $transaction;
  
  /**
   * Constructor to create a wrapper object. This method requires
   * all the necessary information to create a User Points Transaction.
   * 
   * @param string type
   *   The transaction type to execute
   * @param string operation
   *   The user points transaction operation machine ID.
   * @param Drupal\user\Entity\User entity
   *   The target entity for the transaction
   * @param string value
   *   The user points amount for this transaction.
   * @param string qid
   *   The quiz node id.
   */
  public function __construct($type, $operation, User $user, $value, $qid = '') {
    
    $tran = '';
    
    if(!empty($qid)) {
      
      $tran = Transaction::create([
        'type' => $type, 
        'operation' => $operation,
        'target_entity' => $user,
        'field_userpoints_default_amount' => $value,
        'field_tpcext_userpoints_quiz_ref' => $qid,
      ]);
      
    }
    else {
      
      $tran = Transaction::create([
        'type' => $type, 
        'operation' => $operation,
        'target_entity' => $user,
        'field_userpoints_default_amount' => $value,
      ]);
      
    }
    
    $tran->save(); 
    $this->transaction = $tran;
    
  }
  
  /**
   * Executes the transaction and then saves the result.
   */
  public function execute() {
    
    $this->transaction->execute();
    // Saving each one during a bulk execution may bog down execution,
    // it should be considered to call the executeMultiple method in here,
    // and then in the executeMultiple save all the transactions afterward.
    $this->transaction->save();
    
  }
  
}