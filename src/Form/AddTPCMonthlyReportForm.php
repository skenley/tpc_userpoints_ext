<?php

namespace Drupal\tpc_userpoints_ext\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\tpc_userpoints_ext\Entity\TOConfig;
use Drupal\transaction\Entity\TransactionOperation;

class AddTPCMonthlyReportForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    
    return 'tpc_monthly_report_add_form';
    
  }
  
  public function buildForm(array $form, 
    FormStateInterface $formState = NULL) {
    
    //ksm($this->entity);
    //$form['title'] = $this->entity->get('field_tpc_report_title')->view('form');
    $form['#attached']['library'][] = 'tpc_userpoints_ext/tpc-monthly-report-actions';
    
    $form['title'] = array(
      '#title' => 'Report Title',
      '#type' => 'textfield',
      '#maxlength' => 50,
      '#required' => TRUE,
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
      '#type' => 'actions',
    );
    
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
    );
    
    $form['actions']['cancel'] = array(
      '#type' => 'button',
      '#value' => 'Cancel',
    );
    
    return $form;
    
  }
  
  public function submitForm(array &$form, FormStateInterface $formState) {
    
    // TO BE IMPLEMENTED
    
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
      if($confID != 'userpoints_q_quiz_passed') {
        
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
    return $response;
    
  }
  
}