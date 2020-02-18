<?php

namespace Drupal\tpc_userpoints_ext\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;
use Drupal\questionnaire\Access\QuizAccessCheck;
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
              ->condition('status', 1)
              ->execute();
    $quizzes = Node::loadMultiple($quizIDs);
    $quizListArray = [];
    $user = User::load(\Drupal::currentUser()->id());
    $account = \Drupal::currentUser()->getAccount();
    $passedQuizzesRaw = 
      $user->get('field_q_user_passed_quizzes')->getValue();
    $passedQuizzes = [];
    
    foreach($passedQuizzesRaw as $key => $value) {
      
      $passedQuizzes[] = $value['target_id'];
      
    }
    
    foreach($quizzes as $quiz) {
      
      $accessCheck = new QuizAccessCheck();
      
      if(!in_array($quiz->id(), $passedQuizzes) && 
        $accessCheck->access($account, $quiz) != AccessResult::forbidden()) {
      
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