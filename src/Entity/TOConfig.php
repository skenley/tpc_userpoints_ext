<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Transaction Operation Config - The config entity for extra info about a 
 * transaction operation.
 * 
 * @ConfigEntityType(
 *   id = "TOConfig",
 *   label = @Translation("Transaction Operation Config"),
 *   config_prefix = "transaction_operation_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   config_export = {
 *     "id",
 *     "defaultPointValue"
 *   }
 * )
 */
class TOConfig extends ConfigEntityBase implements TOConfigInterface {
  
  protected $id;
  protected $defaultPointValue;
  
  public function getID() {
    
    return $this->id;
    
  }
  
  public function getDefaultPointValue() {
    
    return $this->defaultPointValue;
    
  }
  
  public function setDefaultPointValue($newVal) {
    
    $this->defaultPointValue = $newVal;
    
  }
  
}