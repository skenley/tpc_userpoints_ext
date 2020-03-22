<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Completes the order.
 *
 * @Action(
 *   id = "commerce_order_complete_action",
 *   label = "Complete Order",
 *   type = "commerce_order"
 * )
 */
class OrderCompleteAction extends ActionBase {
  
  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    
    $orderTransitions = $entity->getState()->getTransitions();
    
    // If the order is completed, it will not have any available transitions
    if(sizeof($orderTransitions) > 0) {
      
      $transition = array_keys($orderTransitions)[0];
      
      switch($transition) {
        
        case 'validate':
          // Validate
          $entity->getState()
            ->applyTransition($orderTransitions[$transition]);
          
          // Now, complete
          $completeTransition = $entity->getState()
            ->getTransitions()['fulfill'];
          $entity->getState()->applyTransition($completeTransition);
          $entity->save();
          break;
        case 'fulfill':
          // If we're in this state, the order has already been fulfilled
          // and only needs completed.
          $entity->getState()
            ->applyTransition($orderTransitions[$transition]);
          $entity->save();
          break;
        
      }
      
    }
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $returnAsObject = FALSE) {
    
    // Needs improved to be selective on access
    return true;
    
  }
  
}