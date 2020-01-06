<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the MonthlyReport entity.
 *
 * @ContentEntityType(
 *   id = "tpc_monthly_report",
 *   label = "TPC Monthly Report",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "inline_form" = "Drupal\inline_entity_form\Form\EntityInlineForm",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "tpc_monthly_report",
 *   admin_permission = "administer site configuration",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/tpc_monthly_report/{tpc_monthly_report}",
 *     "edit-form" = "/tpc_monthly_report/{tpc_monthly_report}/edit",
 *     "delete-form" = "/tpc_monthly_report/{tpc_monthly_report}/delete",
 *     "collection" = "/tpc_monthly_report/{tpc_monthly_report}/list",
 *   },
 * )
 */
class MonthlyReport extends ContentEntityBase 
  implements MonthlyReportInterface {
    
  // Implements methods defined by EntityChangedInterface
  use EntityChangedTrait;
  
  public static function baseFieldDefinitions(EntityTypeInterface 
    $entityType) {
    
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel('ID')
      ->setDescription('The ID of the MonthlyReport')
      ->setReadOnly(TRUE);
      
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel('UUID')
      ->setDescription('The UUID of the MonthlyReport')
      ->setReadOnly(TRUE);
      
    // Used the node entity from the core module as a reference for this.
    /*
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel('Title')
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);
      
      $fields['approved'] = BaseFieldDefinition::create('boolean')
        ->setLabel('Approved')
        ->setDescription('Value is checked approved by an admin.')
        ->setDisplayOptions('view', [
          'label' => 'hidden',
          'type' => 'boolean',
          'weight' => 1,
        ])
        ->setDisplayOptions('form', [
          'label' => 'hidden',
          'type' => 'boolean',
          'weight' => 1,
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
        
      $fields['property'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel('Property')
        ->setDescription('The property this monthly report is for.')
        ->setSetting('target_type', 'taxonomy_term')
        ->setSetting('handler_settings', ['target_bundles' => [
            'properties' => 'properties',
          ]
        ])
        ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'entity_reference_label',
          'weight' => 2,
        ))
        ->setDisplayOptions('form', array(
          'label' => 'above',
          'type' => 'entity_reference_autocomplete',
          'settings' => array(
            'match_operator' => 'CONTAINS',
            'size' => 60,
            'autocomplete_type' => 'tags',
            'placeholder' => '',
          ),
          'weight' => 2,
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
      
      $fields['submitting_admin'] = 
        BaseFieldDefinition::create('entity_reference')
          ->setLabel('Admin Submitted')
          ->setDescription('Admin who submitted the report.')
          ->setSetting('target_type', 'user')
          ->setSetting('handler', 'default')
          ->setDisplayOptions('form', array(
          'label' => 'above',
          'type' => 'entity_reference_autocomplete',
          'settings' => array(
            'match_operator' => 'CONTAINS',
            'size' => 60,
            'autocomplete_type' => 'tags',
            'placeholder' => '',
          ),
          'weight' => 3,
          ))
          ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'entity_reference_label',
            'weight' => 3,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);
        
      $fields['approving_manager'] = 
        BaseFieldDefinition::create('entity_reference')
          ->setLabel('Approving Manager')
          ->setDescription('Manager who approved the report.')
          ->setSetting('target_type', 'user')
          ->setSetting('handler', 'default')
          ->setDisplayOptions('form', array(
          'label' => 'above',
          'type' => 'entity_reference_autocomplete',
          'settings' => array(
            'match_operator' => 'CONTAINS',
            'size' => 60,
            'autocomplete_type' => 'tags',
            'placeholder' => '',
          ),
          'weight' => 4,
          ))
          ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'entity_reference_label',
            'weight' => 4,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);
          
      $fields['feedback'] = BaseFieldDefinition::create('text_long')
        ->setLabel('Feedback')
        ->setDescription('Feedback from the admin reviewing the report.')
        ->setDisplayOptions('view', array(
          'label' => 'hidden',
          'type' => 'text_long',
          'weight' => 5,
        ))
        ->setDisplayOptions('form', array(
          'label' => 'above',
          'type' => 'text_long',
          'weight' => 5,
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
          
      $fields['created'] = BaseFieldDefinition::create('created')
        ->setLabel('Date Created')
        ->setDescription('When the report was first created.');
        
      $fields['changed'] = BaseFieldDefinition::create('changed')
        ->setLabel('Date Updated.')
        ->setDescription('The time the report was last updated.');
        
      */
      return $fields;
    
  }
  
}