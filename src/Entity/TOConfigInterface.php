<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface TOConfigInterface extends ConfigEntityInterface {
  
  /**
   * Returns the ID for this entity (the same as the transaction op ID).
   */
  public function getID();
  
  /**
   * Gets the default points value for the Transaction Operation.
   */
  public function getDefaultPointValue();
  
  /**
   * Sets the default point value for the associated Transaction Operation
   */
  public function setDefaultPointValue($newVal);
  
}