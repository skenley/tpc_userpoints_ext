<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Config object to store user point value associated with this quiz.
 *
 * @ConfigEntityType(
 *   id = "QuizConfig",
 *   label = @Translation("Questionnaire Quiz Config"),
 *   config_prefix = "quiz_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   config_export = {
 *     "id",
 *     "pointValue"
 *   }
 * )
 */
class QuizConfig extends ConfigEntityBase implements QuizConfigInterface {
  
  protected $id;
  protected $qid;
  protected $pointValue;
  
  /**
   * {@inheritdoc}
   */
  public function getID() {
    
    return $this->id;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPointValue() {
    
    return $this->pointValue;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function setPointValue($newVal) {
    
    $this->pointValue = $newVal;
    
  }
  
}