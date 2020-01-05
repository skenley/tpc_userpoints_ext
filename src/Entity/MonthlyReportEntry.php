<?php

namespace Drupal\tpc_userpoints_ext\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the MonthlyReportEntry entity.
 *
 * @ContentEntityType(
 *   id = "tpc_monthly_report_entry",
 *   label = "TPC Monthly Report Entry",
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
 *   base_table = "tpc_monthly_report_entry",
 *   admin_permission = "administer site configuration",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/tpc_monthly_report/{tpc_monthly_report_entry}",
 *     "edit-form" = "/tpc_monthly_report/{tpc_monthly_report_entry}/edit",
 *     "delete-form" = "/tpc_monthly_report/{tpc_monthly_report_entry}/delete",
 *     "collection" = "/tpc_monthly_report/{tpc_monthly_report_entry}/list",
 *   },
 * )
 */
class MonthlyReportEntry extends ContentEntityBase 
  implements MonthlyReportEntryInterface {
  
  public static function baseFieldDefinitions(EntityTypeInterface 
    $entityType) {
      
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel('ID')
      ->setDescription('The ID of the Monthly Report Entry')
      ->setReadOnly(TRUE);
      
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel('UUID')
      ->setDescription('The UUID of the Monthly Report Entry')
      ->setReadOnly(TRUE);  
      
    /*  
    $fields['tenant'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel('Tenant')
      ->setDescription('The tenant this entry is for.')
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
      'weight' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
      
    $fields['report'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel('Report')
      ->setDescription('The monthly report this entry is for.')
      ->setSetting('target_type', 'tpc_monthly_report')
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
      'weight' => 1,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
      
    $fields['actions'] = BaseFieldDefinition::create('list_string')
      ->setLabel('Actions')
      ->setDescription('Transactions to execute on the user.')
      ->setSettings(array(
        'allowed_values' => [],
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'list_default',
        'weight' => 2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_buttons',
        'weight' => 2,
      ));
    
    */    
    return $fields;
      
  }
  
}