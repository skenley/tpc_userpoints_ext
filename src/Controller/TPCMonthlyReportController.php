<?php

namespace Drupal\tpc_userpoints_ext\Controller;

use Drupal\Core\Controller\ControllerBase;

class TPCMonthlyReportController extends ControllerBase {
  
  public function confirmPage() {
    
    return [
      '#theme' => 'tpc_monthly_report_confirm_page',
    ];
    
  }
  
}