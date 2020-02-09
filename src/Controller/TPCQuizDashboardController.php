<?php

namespace Drupal\tpc_userpoints_ext\Controller;

use Drupal\Core\Controller\ControllerBase;

class TPCQuizDashboardController extends ControllerBase {
  
  public function dashboardContent() {
    
    $render = [];
    $view = views_embed_view('questionnaire_results', 'block_1');
    $block = \Drupal::service('plugin.manager.block')
              ->createInstance('quiz_list_block', []);
    $blockContent = $block->build();
    $render['quiz_list'] = $blockContent;
    $render['quiz_history']['title'] = [
      '#markup' => '<h2>Quiz History</h2>',
    ];
    $render['quiz_history']['content'] = $view;
    
    return $render;
    
  }
  
}