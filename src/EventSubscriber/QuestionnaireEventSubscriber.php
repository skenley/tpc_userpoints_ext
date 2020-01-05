<?php

namespace Drupal\tpc_userpoints_ext\EventSubscriber;

use Drupal\questionnaire\Event\QuizFirstPassEvent;
use Drupal\tpc_userpoints_ext\Entity\QuizConfig;
use Drupal\tpc_userpoints_ext\UserPointsTransactionWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QuestionnaireEventSubscriber implements EventSubscriberInterface {
  
  public static function getSubscribedEvents() {
    
    return [
      QuizFirstPassEvent::EVENT_NAME => 'quizFirstPassed',
    ];
    
  }
  
  public function quizFirstPassed(QuizFirstPassEvent $event) {
    
    $quiz = $event->getQuiz();
    $user = $event->getUser();
    
    // Load the QuizConfig for this quiz node. If it exists, apply a 
    // userpoints transaction for passing the quiz. Otherwise, do
    // nothing.
    $conf = QuizConfig::load($quiz->id());
    
    if(!empty($conf)) {
      
      $newTran = new UserPointsTransactionWrapper('userpoints_default_points', 
        'userpoints_q_quiz_passed', $user, strval($conf->getPointValue()), 
        $quiz->id());
      $newTran->execute();
      
    }
    
  }
  
}