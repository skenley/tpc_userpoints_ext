<?php

namespace Drupal\tpc_userpoints_ext\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ReviewTPCMonthlyReportForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    
    return 'tpc_monthly_report_review';
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, 
    FormStateInterface $formState = NULL) {
    
    $form['test'] = array(
      '#type' => 'label',
      '#title' => 'This is a test',
    );
    
    return $form;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $formState) {
    
    
    
  }
  
}