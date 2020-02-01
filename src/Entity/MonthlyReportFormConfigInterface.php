<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface MonthlyReportFormConfigInterface extends ConfigEntityInterface {
  
  /**
   * Gets the ID for this config.
   */
  public function getID();
  
  /**
   * Gets the ID of the associated MonthlyReport Entity
   */
  public function getMonthlyReportID();
  
  /**
   * Gets the session ID.
   */
  public function getSessionID();
  
  /**
   * Sets the session ID.
   */
  public function setSessionID($newID);
  
  /**
   * Gets the current page offset of the form.
   */
  public function getCurrentOffset();
  
  /**
   * Sets the current page offset of the form.
   */
  public function setCurrentOffset($newOffset);
  
  /**
   * Gets the pager offset (the number to offset by).
   */
  public function getPagerOffset();
  
  /**
   * Sets the pager offset (the number to offset by).
   */
  public function setPagerOffset($newOffset);
  
  /**
   * Gets the last updated timestamp.
   */
  public function getLastUpdated();
  
  /**
   * Sets the last updated timestamp.
   */
  public function setLastUpdated($newTime);
  
}