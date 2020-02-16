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
use Drupal\Core\Database\Database;

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
    
    //$form['#attached']['library'][] = 'tpc_userpoints_ext/tpc-monthly-report-actions';
    
    $form['instructions'] = array(
      '#theme' => 'tpc_monthly_report_instructions',
    );
    
    $form['report_date'] = array(
      '#type' => 'date',
      '#title' => 'Date of Report',
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
      '#required' => TRUE,
    );
    
    /*
    $form['tenants_container'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => 'tenants-container',
      ),
    );
    
    $form['tenants_container']['tenants'] = array(
      '#markup' => '<label>Tenants</label><p>Select a property to view tenants.</p>',
    );
    
    $form['pagination_container'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => 'paginator',
      ),
    );
    
    $form['pagination_container']['items'] = array(
      '#markup' => '',
    );
    
    $form['test'] = array(
      '#type' => 'textfield',
      
      '#ajax' => [
        'callback' => '::onPagerChange',
        'disable-refocus' => TRUE,
        'event' => 'click',
        'wrapper' => 'test',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Loading...',
        ],
      ],
    );
    */
    $form['actions'] = array(
      '#type' => 'actions',
    );
    
    $form['actions']['continue'] = array(
      '#type' => 'submit',
      '#value' => 'Continue',
    );
    
    return $form;
    
  }
  
  public function submitForm(array &$form, FormStateInterface $formState) {
    
    $url = \Drupal\Core\Url
      ::fromRoute('tpc_userpoints_ext.tpc_monthly_report_add_tenants');
    $url->setRouteParameters([
      'tid' => $formState->getValues()['property'],
      'date' => $formState->getValues()['report_date'],
    ]);
        
    $formState->setRedirectUrl($url);
    
  }
  
  /*
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
    
    $paginator = [
      '#theme' => 'tpc_monthly_report_pager_item',
      '#item' => 'previous',
      '#ajax' => [
        'callback' => '::onPagerChange',
        'disable-refocus' => TRUE,
        'event' => 'click',
        'wrapper' => 'pagination_container',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Loading...',
        ],
      ],
    ];
    
    $paginator = [
      '#type' => 'label',
      '#title' => 'Previous',
      '#ajax' => [
        'callback' => '::onPagerChange',
        'disable-refocus' => TRUE,
        'event' => 'click',
        'wrapper' => 'pagination_container',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Loading...',
        ],
      ],
    ];
    
    $output = \Drupal::service('renderer')->render($template);
    $paginatorOutput = \Drupal::service('renderer')->render($paginator);
    
    $render = [];
    $response->addCommand(new HtmlCommand('#edit-tenants-container', $output));
    $response->addCommand(new HtmlCommand('#edit-pagination-container', $paginatorOutput));
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
  
  public function onPagerChange(&$form, FormStateInterface $formState) {
    
    \Drupal::logger('tpc')->notice('What a message!');
    ksm('Hey!');
    $response = new AjaxResponse();
    //$response->addCommand(new InvokeCommand('.pager-count',
    //    'addClass', ['test-class']));
    return $response;
    
  }
  */
}