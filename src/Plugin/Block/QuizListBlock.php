<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Block that displays a list of quizzes that the current user can take.
 * This list consists of quizzes that haven't been taken yet, or quizzes
 * that have been taken but not yet passed.
 *
 * @Block(
 *   id = "quiz_list_block",
 *   admin_label = "Quiz List Block",
 *   category = "TPC Userpoints Extension",
 * )
 */
class QuizListBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $quizIDs = \Drupal::entityQuery('node')
              ->condition('type','q_quiz')
              ->execute();
    $quizzes = Node::loadMultiple($quizIDs);
    $quizListArray = [];
    $user = User::load(\Drupal::currentUser()->id());
    $passedQuizzesRaw = 
      $user->get('field_q_user_passed_quizzes')->getValue()[0];
    $passedQuizzes = [];
    
    foreach($passedQuizzesRaw as $key => $value) {
      
      $passedQuizzes[] = $value;
      
    }
    
    foreach($quizzes as $quiz) {
      
      if(!in_array($quiz->id(), $passedQuizzes)) {
      
        $i = count($quizListArray);
        
        $quizListArray[$i]['title'] = $quiz->getTitle();
        $quizListArray[$i]['icon'] = \Drupal::service('renderer')
                                    ->renderRoot($quiz
                                      ->get('field_q_quiz_icon')
                                      ->view());
        $quizListArray[$i]['link'] = '/questionnaire/quiz/' . $quiz->id();
      
      }

    }
    
    return [
      '#theme' => 'tpc_quiz_list',
      '#quizList' => $quizListArray,
    ];
    
  }
  
}