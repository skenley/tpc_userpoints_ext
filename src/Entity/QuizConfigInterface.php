<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface QuizConfigInterface extends ConfigEntityInterface {
  
  /**
   * Gets the ID of the quiz node for this config.
   */
  public function getID();
  
  /**
   * Gets the user point value associated with the quiz.
   */
  public function getPointValue();
  
  /**
   * Sets the user point value associated with the quiz.
   */
  public function setPointValue($newVal);
  
}