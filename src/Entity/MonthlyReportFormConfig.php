<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Config object to store user point value associated with this quiz.
 *
 * @ConfigEntityType(
 *   id = "MonthlyReportFormConfig",
 *   label = @Translation("Monthly Report Form Config"),
 *   config_prefix = "monthly_report_form_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   config_export = {
 *     "id",
 *     "monthlyReportID",
 *     "sessionID",
 *     "currentOffset",
 *     "pagerOffset",
 *     "lastUpdated",
 *   }
 * )
 */
class MonthlyReportFormConfig extends ConfigEntityBase 
  implements MonthlyReportFormConfigInterface {
  
  protected $id;
  protected $monthlyReportID;
  protected $sessionID;
  protected $currentOffset;
  protected $pagerOffset;
  protected $lastUpdated;
  
  /**
   * {@inheritdoc}
   */
  public function getID() {
    
    return $this->id;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getMonthlyReportID() {
    
    return $this->monthlyReportID;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getSessionID() {
    
    return $this->sessionID;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function setSessionID($newID) {
    
    $this->sessionID = $newID;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getCurrentOffset() {
    
    return $this->currentOffset;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function setCurrentOffset($newOffset) {
    
    $this->currentOffset = $newOffset;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPagerOffset() {
    
    return $this->pagerOffset;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function setPagerOffset($newOffset) {
    
    $this->pagerOffset = $newOffset;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getLastUpdated() {
    
    return $this->lastUpdated;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function setLastUpdated($newTime) {
    
    $this->lastUpdated = $newTime;
    
  }
  
}