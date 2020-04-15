<?php

/**
 * @file
 * Contains \Drupal\tpc_userpoints_ext\Plugin\Block\LeaderboardBlock
 */

namespace Drupal\tpc_userpoints_ext\Plugin\Block;

use \DateTime;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Database;

/**
 * Provides a 'Top 10 Leaderboard' list block
 * 
 * @Block(
 *   id = "leaderboard_block",
 *   admin_label = @Translation("Leaderboard Top 10 Block"),
 *   category = "TPC Userpoints Extension",
 *  )
 */

class LeaderboardBlock extends BlockBase {
  /**
   * Gets top 10 users for leaderboard.
   *
   * @return array
   */
  protected function load() {
    $firstDay = new DateTime('first day of january');
    $firstDaySec = $firstDay->format('U');
    $database = \Drupal::database();
    $database->query("SET SESSION sql_mode = ''")->execute();
    
    // Select base table for query.
    $select = $database->select('transaction', 't');
    // Join the userpoints default amount table so we can get the amount of each transaction.
    $select->join('transaction__field_userpoints_default_amount', 'ta', 't.id = ta.entity_id');
    // Join the user roles table so we can get the target entity's role.
    $select->join('user__roles', 'ur', 't.target_entity__target_id = ur.entity_id');
    // Join the users property table so we can get the target entity's property.
    $select->join('user__field_user_property', 'p', 'p.entity_id = t.target_entity__target_id');
    // Join the Taxonomy table so we can get the property name.
    $select->join('taxonomy_term_field_data', 'pn', 'p.field_user_property_target_id = pn.tid');
    // Join the users screen name table so we can get the target entity's screen name.
    $select->join('user__field_screen_name', 'sn', 't.target_entity__target_id = sn.entity_id');
    // Select the user screen name and property name.
    $select->addField('sn', 'field_screen_name_value');
    $select->addField('pn', 'name');
    // Groupl results by the User ID.
    $select->groupBy('t.target_entity__target_id');
    // Get the sum of all the points earnded YTD for each user.
    $select->addExpression('SUM(ta.field_userpoints_default_amount_value)', 'total');
    // Do not select transactions that haven't been executed.
    $select->isNotNull('t.executed');
    // Only select transactions attached to users with the tenant role.
    $select->condition('ur.roles_target_id', 'tenant', '=');
    // Only select transactions that were created during the current year.
    $select->condition('t.created', $firstDaySec, '>=');
    // Do not select transactions using the Admin, or Commerce operations.
    $select->condition('t.operation', 'userpoints_default_admin', '<>');
    $select->condition('t.operation', 'userpoints_commerce_transaction', '<>');
    // Order the results by total points descending.
    $select->orderBy('total', 'DESC');
    // Limit to the top 10 results.
    $select->range(0, 10);
    
    $results = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $results;
  }
  
  /**
  * {@inheritdoc}
  */
  
  public function build() {
    $content['message'] = array(
      '#prefix' => '<p class="lb-intro">',
      '#markup' => $this->t("Like to compete? So do we. Participating residents can see how their points rank against others, and receive even more points if they're one of the Top 20 point-earners at year end."),
      '#suffix' => '</p>',
    );

    $headers = array(
      t(''),
      t('Screen Name'),
      t('Property'),
      t('Points'),
    );

    $rows = array();
    foreach ($results = $this->load() as $key=>$result) {
      $result = array_merge(array('rank' => $key + 1), $result);
      $rowClass = $key % 2 == 0 ? 'even' : 'odd';
      $rows[] = $result;
      $rows[$key] = array('data' => $result, 'class' => array($rowClass));
    }

    $content['table'] = array(
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => t('No records available, check back soon!'),
      '#attributes' => array(
        'class' => array(
          'leader-board',
        ),
      ),
    );
    $content['#cache']['max-age'] = 0;
    return $content;
  }
}