<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Block that displays a message if any quizzes are available to take
 * or not and a button that links to the Quiz Dashboard.
 *
 * @Block(
 *   id = "quiz_button_block",
 *   admin_label = "Quiz Button Block",
 *   category = "TPC Userpoints Extension",
 * )
 */
class QuizButtonBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $quizIDs = \Drupal::entityQuery('node')
      ->condition('type', 'q_quiz')
      ->condition('status', 1)
      ->execute();
    $quizzes = Node::loadMultiple($quizIDs);
    $quizListArray = [];
    $user = User::load(\Drupal::currentUser()->id());
    $passedQuizzesRaw =
      $user->get('field_q_user_passed_quizzes')->getValue();
    $passedQuizzes = [];
    
    foreach($passedQuizzesRaw as $key => $value) {
      
      $passedQuizzes[] = $value['target_id'];
      
    }
    
    foreach($quizzes as $quiz) {
      
      if(!in_array($quiz->id(), $passedQuizzes)) {
        
        return [
          '#theme' => 'tpc_quiz_button',
          '#quizButton' => 'TRUE',
        ];
      } 
    }
    
    return [
      '#theme' => 'tpc_quiz_button',
      '#quizButton' => 'FALSE',
    ];
  }
  
  public function getCacheTags() {
    
    return Cache::mergeTags(parent::getCacheTags(), ['tpc_quiz_button_block']);
    
  }
  
}