<?php

namespace Drupal\tpc_userpoints_ext\Controller;

use Drupal\Core\Controller\ControllerBase;

class TPCQuizDashboardController extends ControllerBase {
  
  public function dashboardContent() {
    
    return [
      '#markup' => '<p>Hello world!</p>',
    ];
    
  }
  
}