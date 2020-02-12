<?php

namespace Drupal\tpc_userpoints_ext\Controller;

use Drupal\Core\Controller\ControllerBase;

class TPCMonthlyReportController extends ControllerBase {
  
  public function confirmPageNew() {
    
    return [
      '#theme' => 'tpc_monthly_report_confirm_page',
      '#message' => 'Thank you for your submission! Your report will be ' . 
        'reviewed by an administrator.'
    ];
    
  }
  
  public function confirmPageApproved() {
    
    return [
      '#theme' => 'tpc_monthly_report_confirm_page',
      '#message' => 'The report has been approved! ',
    ];
    
  }
  
  public function confirmPageSaved() {
    
    return [
      '#theme' => 'tpc_monthly_report_confirm_page',
      '#message' => 'Your changes have been successfully saved.',
    ];
    
  }
  
}