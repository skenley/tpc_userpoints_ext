<?php

namespace Drupal\tpc_userpoints_ext\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class AddTPCMonthlyReportForm extends ContentEntityForm {
  
  public function buildForm(array $form, 
    FormStateInterface $formState = NULL) {
    
    //ksm($this->entity);
    //$form['title'] = $this->entity->get('field_tpc_report_title')->view('form');
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
  
  public function save(array $form, FormStateInterface $formState) {
    
    // TO BE IMPLEMENTED
    
  }
  
}