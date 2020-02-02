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

class AddTPCMonthlyReportFormTenants extends FormBase {
  
  private $users;
  private $actions;
  private $pagerOffset;
  private $currentOffset;
  private $property;
  private $date;
  private $title;
  private $pagerConfigID;
  
  public function __construct() {
    
    $this->property = Term::load($_GET['tid']);
    $this->date = date_create($_GET['date']);
    $this->title = $this->property->getName() . ' ' . 
      date_format($this->date, 'F Y');
    $this->pagerConfigID = \Drupal::currentUser()->id() . 
      date_format($date, 'm-Y') . 
      $this->property->getName() . 
      $tid;
    $this->pagerConfigID = hash('sha256', $this->pagerConfigID);
    $this->users = [];
    $this->actions = [];
    
    $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
    
    if(empty($pagerConfig)) {

      $this->pagerOffset = 5;
      $this->currentOffset = 0;
    
    }
    else {
      
      $this->pagerOffset = $pagerConfig->getPagerOffset();
      $this->currentOffset = $pagerConfig->getCurrentOffset();
      
    }
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    
    return 'tpc_monthly_report_add_form_tenants';
    
  }
  
  public function buildForm(array $form, 
    FormStateInterface $formState = NULL) {
    $entries = MonthlyReportEntry::loadMultiple();
    //ksm($entries);
    /*
    foreach($entries as $entry) {
      ksm($entry->get('field_tpc_re_actions')->getValue());
      //$entry->delete();
    }
    */
    $reportsCount = \Drupal::database()
              ->select('tpc_monthly_report', 'tpcmr')
              ->fields('tpcmr', ['id'],)
              ->countQuery()
              ->execute()
              ->fetchField();
    $tid = $_GET['tid'];
    $date = date_create($_GET['date']);
    $property = Term::load($tid);
    $formTitle = $property->getName() . ' ' . date_format($date, 'F Y');
    $formConfigID = \Drupal::currentUser()->id() . 
      date_format($date, 'm-Y') . 
      $property->getName() . 
      $tid;
    $formConfigID = hash('sha256', $formConfigID);
    $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
    
    if(!empty($formState)) {
      //ksm($formState->getResponse());
      //ksm(new Response());
      if($formState->getTriggeringElement()['#value'] == 'Next') {
        
        $this->currentOffset += $this->pagerOffset;
        
      }
    }
    
    // Set the title
    $form['#title'] = 'Report For ' . $this->title;
    $userIndex = 0;
    
    $tmpUsers = \Drupal::entityTypeManager()
            ->getListBuilder('user')
            ->getStorage()
            ->loadByProperties([
              'field_user_property' => $this->property->id(),
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
    /*
    $reports = MonthlyReport::loadMultiple();
    $entries = MonthlyReportEntry::loadMultiple();
    //ksm($reports);
    //ksm($entries);
    
    foreach($entries as $entry) {
      
      //$entry->delete();
      //ksm($entry->get('field_tpc_re_report')->getValue());
      
    }
    
    foreach($reports as $report) {
      
      $report->delete();
      
    }
    */
    
    //$fieldConfig = FieldStorageConfig::loadByName('tpc_monthly_report_entry', 'field_tpc_re_actions');
    //$tmpAllowed = [];
    //ksm($fieldConfig);
    /*
    foreach($this->actions as $action) {
      
      $tmpAllowed[$action['id']] = $action['label'];
      
    }
    
    $fieldConfig->setSetting('allowed_values', $tmpAllowed);
    $fieldConfig->save();
    */
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
      $defaultCheckValues = [];
      
      if(!empty($pagerConfig)) {
        
        $reportEntries = \Drupal::entityQuery('tpc_monthly_report_entry')
                              ->condition('field_tpc_re_report', 
                                $pagerConfig->getMonthlyReportID())
                              ->condition('field_tpc_re_tenant', 
                                explode('_', $tenantID)[1])
                              ->execute();
        
        if(!empty($reportEntries)) {
          
          $key = array_keys($reportEntries)[0];
          $reportEntry = MonthlyReportEntry::load($reportEntries[$key]);
          $checkedOptions = $reportEntry->get('field_tpc_re_actions')->getValue();
          
          foreach($checkedOptions as $option) {
            
            $defaultCheckValues[] = $option['value'];
            
          }
          
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
    
    return $form;
    
  }
  
  public function submitForm(array &$form, FormStateInterface $formState) {
    
    /*
    $url = \Drupal\Core\Url
      ::fromRoute('tpc_userpoints_ext.tpc_monthly_report_add_confirm');
    ksm($formState->getValues());
        
    $formState->setRedirectUrl($url);
    */
    
    $buttonSubmitted = $formState->getTriggeringElement()['#value'];
    
    if($buttonSubmitted == 'Next') {
      
      $pagerConfig = MonthlyReportFormConfig::load($this->pagerConfigID);
      
      if(empty($pagerConfig)) {
        
        $reportsCount = \Drupal::database()
              ->select('tpc_monthly_report', 'tpcmr')
              ->fields('tpcmr', ['id'],)
              ->countQuery()
              ->execute()
              ->fetchField();
        $reportsCount = intval($reportsCount);
        
        // Find a unique id that can be used for the new monthly report
        while(true) {
          
          $tmp = MonthlyReport::load(strval($reportsCount));
          
          if(empty($tmp)) {
            
            break;
            
          }
          else {
            
            $reportsCount++;
            
          }
          
        }
        
        $reportsCount = strval($reportsCount);
        $createdTimestamp = time();
        
        $newMonthlyReport = MonthlyReport::create([
          'id' => $reportsCount,
          'field_tpc_report_title' => $this->title,
          'field_tpc_report_property' => $this->property->id(),
          'field_tpc_report_created' => $createdTimestamp,
          'field_tpc_report_changed' => $createdTimestamp,
        ]);
        $newMonthlyReport->save();
        
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
                              $reportsCount)
                            ->condition('field_tpc_re_tenant', $tenantID)
                            ->execute();
          
          if(empty($reportEntries)) {
            
            $reportEntry = MonthlyReportEntry::create([
              'id' => $entryID,
              'field_tpc_re_report' => 
                $newMonthlyReport->id(),
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
        
        $pagerConfig = MonthlyReportFormConfig::create([
          'id' => $this->pagerConfigID,
          'monthlyReportID' => $newMonthlyReport->id(),
          'currentOffset' => $this->currentOffset + $this->pagerOffset,
          'pagerOffset' => 5,
          'lastUpdated' => $createdTimestamp,
        ]);
        $pagerConfig->save();
        
      }
      else {
        
        // Update pager
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
  
}