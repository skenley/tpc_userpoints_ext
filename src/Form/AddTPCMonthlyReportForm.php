<?php

namespace Drupal\tpc_userpoints_ext\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\tpc_userpoints_ext\Entity\TOConfig;
use Drupal\transaction\Entity\TransactionOperation;

class AddTPCMonthlyReportForm extends FormBase {
  
  private $noTenants;
  
  /**
   * {@inheritdoc}
   */
  public function __construct() {
    
    $this->noTenants = TRUE;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    
    return 'tpc_monthly_report_add_form';
    
  }
  
  public function buildForm(array $form, 
    FormStateInterface $formState = NULL) {
    
    $form['#attached']['library'][] = 'tpc_userpoints_ext/tpc-monthly-report-actions';
    
    $form['report_date'] = array(
      '#type' => 'date',
      '#title' => 'Date of Report',
    );
    
    $form['property'] = array(
      '#type' => 'entity_autocomplete',
      '#title' => 'Property',
      '#description' => 'The community this report is for.',
      '#target_type' => 'taxonomy_term',
      '#selection_handler' => 'default',
      '#selection_settings' => [
        'target_bundles' => ['properties'],
      ],
      '#ajax' => [
        'callback' => '::onPropertyChange',
        'disable-refocus' => TRUE,
        'event' => 'change',
        'wrapper' => 'property',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Loading...',
        ],
      ],
    );
    
    $form['tenants_container'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => 'tenants-container',
      ),
    );
    
    $form['tenants_container']['tenants'] = array(
      '#markup' => '<label>Tenants</label><p>Select a property to view tenants.</p>',
    );
    
    $form['actions'] = array(
      '#theme' => 'tpc_monthly_report_actions',
      '#type' => 'submit',
    );
    
    return $form;
    
  }
  
  public function submitForm(array &$form, FormStateInterface $formState) {
    
    $url = \Drupal\Core\Url
      ::fromRoute('tpc_userpoints_ext.tpc_monthly_report_add_confirm');
        
    $formState->setRedirectUrl($url);
    
  }
  
  public function onPropertyChange(&$form, FormStateInterface $formState) {
    
    // Load the tenants who are at the property
    $propertyID = $formState->getValues()['property'];
    $tmpUsers = \Drupal::entityTypeManager()
            ->getListBuilder('user')
            ->getStorage()
            ->loadByProperties([
              'field_user_property' => $propertyID,
            ]);
    $users = [];
    $actions = [];
    
    foreach($tmpUsers as $userID => $tmpUser) {
      
      $users[] = [
        'first_name' => $tmpUser->get('field_user_first_name')
          ->getValue()[0]['value'],
        'last_name' => $tmpUser->get('field_user_last_name')
          ->getValue()[0]['value'],
        'email' => $tmpUser->get('mail')
          ->getValue()[0]['value'],
        'unique_id' => $tmpUser->get('field_user_unique_id')
          ->getValue()[0]['value'],
        'unit_num' => $tmpUser->get('field_user_property_unit_number')
          ->getValue()[0]['value'],
      ];
      
    }
    
    // Load the transaction operations that can be applied to each user
    $operationConfigs = TOConfig::loadMultiple();
    
    foreach($operationConfigs as $conf) {
      
      $confID = $conf->id();
      
      // If the transaction operation is not apart of the list to be 
      // excluded, include it in the action list.
      if($confID != 'userpoints_q_quiz_passed' 
        && $confID != 'userpoints_commerce_transaction') {
        
        $actions[] = [
          'id' => $conf->id(),
          'label' => TransactionOperation::load($conf->id())->label(),
        ];
        
      }
      
    }
    
    // Format the AJAX response
    $response = new AjaxResponse();
    $template = [
      '#theme' => 'tpc_monthly_report_tenants',
      '#tenants' => $users,
      '#actions' => $actions,
    ];
    $output = \Drupal::service('renderer')->render($template);
    $response->addCommand(new HtmlCommand('#edit-tenants-container', $output));
    
    // If there were no tenants in the previous load and there are tenants
    // loaded for this AJAX call, hide the continue button and show the
    // action buttons.
    if($this->noTenants && count($users) > 0) {
    
      $response->addCommand(new InvokeCommand('.actions .secondary-actions', 
        'removeClass', ['hide']));
      $response->addCommand(new InvokeCommand('.actions .initial-actions',
        'addClass', ['hide']));
      $this->noTenants = FALSE;
      
    }
    // If there were tenants loaded in the last load but no tenants in this
    // load, hide the actions and show the continue button
    else if(!$noTenants && count($users) == 0) {
      
      $response->addCommand(new InvokeCommand('.actions .secondary-actions', 
        'addClass', ['hide']));
      $response->addCommand(new InvokeCommand('.actions .initial-actions',
        'removeClass', ['hide']));
      $this->noTenants = TRUE;
      
    }
    
    return $response;
    
  }
  
}