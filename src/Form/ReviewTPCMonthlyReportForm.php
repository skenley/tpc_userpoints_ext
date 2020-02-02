<?php

namespace Drupal\tpc_userpoints_ext\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\user\Entity\User;
use Drupal\tpc_userpoints_ext\Entity\TOConfig;
use Drupal\tpc_userpoints_ext\Entity\MonthlyReport;
use Drupal\tpc_userpoints_ext\Entity\MonthlyReportEntry;
use Drupal\tpc_userpoints_ext\Entity\MonthlyReportFormConfig;
use Drupal\transaction\Entity\TransactionOperation;
use Drupal\Core\Database\Database;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ReviewTPCMonthlyReportForm extends FormBase {
  
  private $users;
  private $actions;
  private $pagerOffset;
  private $currentOffset;
  private $pagerConfigID;
  
  public function __construct() {
    
    $this->pagerConfigID = '';
    $this->users = [];
    $this->actions = [];
    
  }
  
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
    
    $reportID = $_GET['report'];
    
    // Make sure the report exists first before loading.
    if(!empty($reportID)) {
      
      $report = MonthlyReport::load($reportID);
      
      if(empty($report)) {
        
        $form['error'] = $this->getRequirementMessage();
        return $form;
        
      }
      else {
        
        $reportEntries = MonthlyReportEntry::loadMultiple();
        
        $formConfigID = \Drupal::currentUser()->id() . 
          $report->get('field_tpc_report_title')
            ->getValue()[0]['value'];
        $this->pagerConfigID = $formConfigID;
        $this->pagerConfigID = hash('sha256', $this->pagerConfigID);
        $formConfigID = hash('sha256', $formConfigID);
        $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
        
        if(empty($pagerConfig)) {

          $this->pagerOffset = 5;
          $this->currentOffset = 0;
        
        }
        else {
          
          $this->pagerOffset = $pagerConfig->getPagerOffset();
          $this->currentOffset = $pagerConfig->getCurrentOffset();
          
        }
        
        $userIndex = 0;
        $tmpUsers = \Drupal::entityTypeManager()
                ->getListBuilder('user')
                ->getStorage()
                ->loadByProperties([
                  'field_user_property' => $report
                    ->get('field_tpc_report_property')
                    ->getValue()[0]['target_id'],
                ]);
                
        foreach($tmpUsers as $userID => $tmpUser) {
          
          $this->users[$userIndex] = [
            'first_name' => $tmpUser->get('field_user_first_name')
              ->getValue()[0]['value'],
            'last_name' => $tmpUser->get('field_user_last_name')
              ->getValue()[0]['value'],
            'unit_num' => $tmpUser->get('field_user_property_unit_number')
              ->getValue()[0]['value'],
            'id' => $tmpUser->id(),
            'actions' => [],
          ];
          $userIndex++;
          
        }
        
        // Load the transaction operations that can be applied to each user
        $operationConfigs = TOConfig::loadMultiple();
        foreach($operationConfigs as $conf) {
          
          $confID = $conf->id();
          
          // If the transaction operation is not apart of the list to be 
          // excluded, include it in the action list.
          if($confID != 'userpoints_q_quiz_passed' 
            && $confID != 'userpoints_commerce_transaction') {
            
            $this->actions[] = [
              'id' => $conf->id(),
              'label' => TransactionOperation::load($conf->id())->label(),
            ];
            
          }
          
        }
        
        $form['actions_container'] = array(
          '#type' => 'container',
          '#attributes' => array(
            'class' => 'actions-container',
          ),
        );
        
        $form['actions_container']['heading'] = array(
          '#type' => 'label',
          '#title' => 'Apply to all',
        );
        
        $form['tenants_container'] = array(
          '#type' => 'container',
          '#attributes' => array(
            'class' => 'tenants-container',
          ),
          '#tree' => TRUE,
        );
        
        $form['tenants_container']['heading'] = array(
          '#type' => 'label',
          '#title' => 'Tenants',
        );
        
        $actionCheckboxes = array();
    
        foreach($this->actions as $action) {
          
          $actionCheckboxes[$action['id']] = $action['label'];
          
        }
        
        $form['actions_container']['global_actions']
              = array(
                '#type' => 'checkboxes',
                '#options' => $actionCheckboxes,
                '#attributes' => array(
                  'class' => 'global_action',
                ),
              );
              
        foreach($this->users as $uIndex => $user) {
      
          if($uIndex < $this->currentOffset ||
            $uIndex >= ($this->currentOffset + $this->pagerOffset)) {
            
            continue;
            
          }
          
          $tenantID = 'tenant_' . $user['id'];
          $tenantIDNum = explode('_', $tenantID)[1];
          $defaultCheckValues = [];
          $reportEntries = \Drupal::entityQuery('tpc_monthly_report_entry')
                                ->condition('field_tpc_re_report', $reportID)
                                ->condition('field_tpc_re_tenant', $tenantIDNum)
                                ->execute();

          $key = array_keys($reportEntries)[0];
          $reportEntry = MonthlyReportEntry::load($reportEntries[$key]);
          
          if(!empty($reportEntry)) {
          
            $checkedOptions = $reportEntry->get('field_tpc_re_actions')->getValue();
            
            foreach($checkedOptions as $option) {
              
              $defaultCheckValues[] = $option['value'];
              
            }
            
          }
          
          $form['tenants_container'][$tenantID] = array(
            '#type' => 'container',
            '#attributes' => array(
              'class' => 'tenant_wrapper',
            ),
            '#tree' => TRUE,
          );
          
          $form['tenants_container'][$tenantID]['info'] = array(
            '#type' => 'container',
            '#attributes' => array(
              'class' => 'info',
            ),
          );
          
          $form['tenants_container'][$tenantID]['info']['content'] = array(
            '#type' => 'label',
            '#title' => $user['unit_num'] . ' - ' . $user['first_name'] . ' ' .
              $user['last_name'],
          );
          
          $form['tenants_container'][$tenantID]['local_actions'] = array(
            '#type' => 'container',
            '#attributes' => array(
              'class' => 'local_actions',
            ),
          );
          
          $form['tenants_container'][$tenantID]['local_actions']
              = array(
                '#type' => 'checkboxes',
                '#options' => $actionCheckboxes,
                '#default_value' => $defaultCheckValues,
                '#attributes' => array(
                  'class' => 'local_action',
                ),
              );
          
        }
        
        $form['tenants_container']['actions'] = array(
          '#type' => 'actions'
        );
        
        if($this->currentOffset != 0) {
          
          $form['tenants_container']['actions']['previous'] = array(
            '#type' => 'submit',
            '#value' => 'Previous',
          );
          
        }
        
        if(($this->currentOffset + $this->pagerOffset) < count($this->users)) {
          
          $form['tenants_container']['actions']['next'] = array(
            '#type' => 'submit',
            '#value' => 'Next',
          );
          
        }
        else {
          
          $form['tenants_container']['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => 'Submit For Approval',
          );
          
        }
        
        $form['#attached']['library'][] = 
          'tpc_userpoints_ext/tpc-monthly-report-actions';
        
      }
      
    }
    else {
      
      $form['error'] = $this->getRequirementMessage();
      
    }
    
    return $form;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $formState) {
    
    $buttonSubmitted = $formState->getTriggeringElement()['#value'];
    $report = MonthlyReport::load($_GET['report']);
    $formConfigID = \Drupal::currentUser()->id() . 
    $report->get('field_tpc_report_title')
      ->getValue()[0]['value'];
    $this->pagerConfigID = $formConfigID;
    $this->pagerConfigID = hash('sha256', $this->pagerConfigID);
    
    if($buttonSubmitted == 'Next') {
      
      $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
      
      if(empty($pagerConfig)) {
        
        $createdTimestamp = date();
        $report->set('field_tpc_report_changed', $createdTimestamp);
        $report->save();
        
        // Save user checkbox states
        foreach($formState->getValues()['tenants_container'] as $tenantKey => $values) {
          
          $tenantID = explode('_', $tenantKey)[1];
          
          // If this isn't in place a config will be added for the actions.
          if(!is_numeric($tenantID)) {
            
            continue;
            
          }
          
          $checkedActions = [];
          $entryID = 0;
          
          foreach($values['local_actions'] as $action => $checked) {
            
            if($checked) {
              
              $checkedActions[] = $action;
              
            }
            
          }
          
          $reportEntries = \Drupal::entityQuery('tpc_monthly_report_entry')
                            ->condition('field_tpc_re_report', 
                              $report->id())
                            ->condition('field_tpc_re_tenant', $tenantID)
                            ->execute();
          
          $key = array_keys($reportEntries)[0];
          $reportEntry = MonthlyReportEntry::load($reportEntries[$key]);
          $reportEntry->set('field_tpc_re_actions', $checkedActions);
          
          $reportEntry->save();
          
        }
        
        $pagerConfig = MonthlyReportFormConfig::create([
          'id' => $this->pagerConfigID,
          'monthlyReportID' => $report->id(),
          'currentOffset' => $this->currentOffset + $this->pagerOffset,
          'pagerOffset' => 5,
          'lastUpdated' => $createdTimestamp,
        ]);
        $pagerConfig->save();
        
      }
      else {
        
        // Update pager
        $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
        $pagerConfig->setCurrentOffset(
          $this->currentOffset + $this->pagerOffset);
        $pagerConfig->setLastUpdated(time());
        $pagerConfig->save();
        
        // Save user checkbox states
        foreach($formState->getValues()['tenants_container'] as $tenantKey => $values) {
          
          $tenantID = explode('_', $tenantKey)[1];
          
          // If this isn't in place a config will be added for the actions.
          if(!is_numeric($tenantID)) {
            
            continue;
            
          }
          
          $checkedActions = [];
          $entryID = 0;
          
          foreach($values['local_actions'] as $action => $checked) {
            
            if($checked) {
              
              $checkedActions[] = $action;
              
            }
            
          }
          
          $reportEntries = \Drupal::entityQuery('tpc_monthly_report_entry')
                            ->condition('field_tpc_re_report', 
                              $report->id())
                            ->condition('field_tpc_re_tenant', $tenantID)
                            ->execute();
          
          $key = array_keys($reportEntries)[0];
          $reportEntry = MonthlyReportEntry::load($reportEntries[$key]);
          $reportEntry->set('field_tpc_re_actions', $checkedActions);
          
          $reportEntry->save();
          
        }
        
      }
      
    }
    else if($buttonSubmitted == 'Previous') {
      
      $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
      $pagerConfig->setCurrentOffset(
        $this->currentOffset - $this->pagerOffset);
      $pagerConfig->save();
      
      // Save user checkbox states
      foreach($formState->getValues()['tenants_container'] as $tenantKey => $values) {
        
        $tenantID = explode('_', $tenantKey)[1];
        
        // If this isn't in place a config will be added for the actions.
        if(!is_numeric($tenantID)) {
          
          continue;
          
        }
        
        $checkedActions = [];
        $entryID = 0;
        
        foreach($values['local_actions'] as $action => $checked) {
          
          if($checked) {
            
            $checkedActions[] = $action;
            
          }
          
        }
        
        $reportEntries = \Drupal::entityQuery('tpc_monthly_report_entry')
                          ->condition('field_tpc_re_report', 
                            $report->id())
                          ->condition('field_tpc_re_tenant', $tenantID)
                          ->execute();
        
        $key = array_keys($reportEntries)[0];
        $reportEntry = MonthlyReportEntry::load($reportEntries[$key]);
        $reportEntry->set('field_tpc_re_actions', $checkedActions);
        
        $reportEntry->save();
        
      }
      
    }
    else if($buttonSubmitted == 'Submit For Approval') {
      
      $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
      
      // Save any user operations that were checked on this page.
      foreach($formState->getValues()['tenants_container'] as $tenantKey => $values) {
        
        $tenantID = explode('_', $tenantKey)[1];
        
        // If this isn't in place a config will be added for the actions.
        if(!is_numeric($tenantID)) {
          
          continue;
          
        }
        
        $checkedActions = [];
        $entryID = 0;
        
        foreach($values['local_actions'] as $action => $checked) {
          
          if($checked) {
            
            $checkedActions[] = $action;
            
          }
          
        }
        
        $reportEntries = \Drupal::entityQuery('tpc_monthly_report_entry')
                          ->condition('field_tpc_re_report', 
                            $pagerConfig->getMonthlyReportID())
                          ->condition('field_tpc_re_tenant', $tenantID)
                          ->execute();
        
        if(empty($reportEntries)) {
          
          $reportEntry = MonthlyReportEntry::create([
            'id' => $entryID,
            'field_tpc_re_report' => $pagerConfig->getMonthlyReportID(),
            'field_tpc_re_actions' => $checkedActions,
            'field_tpc_re_tenant' => $tenantID,
          ]);
          
        }
        else {
          
          $key = array_keys($reportEntries)[0];
          $reportEntry = MonthlyReportEntry::load($reportEntries[$key]);
          $reportEntry->set('field_tpc_re_actions', $checkedActions);
          
        }
        
        $reportEntry->save();
        
      }
      
      // Clean up the config object to make sure it doesn't just
      // occupy space in the database
      $pagerConfig->delete();
      
      $url = \Drupal\Core\Url
        ::fromRoute('tpc_userpoints_ext.tpc_monthly_report_add_confirm');
        
      $formState->setRedirectUrl($url);
    }
    
  }
  
  public function getRequirementMessage() {
    
    return array(
      '#type' => 'label',
      '#title' => 'A valid report ID is required to review a report.',
    );
    
  }
  
}