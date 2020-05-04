<?php
/**
 * @file
 * Contains \Drupal\tpc_userpoints_ext\Plugin\QueueWorker\TPCUserpointsQueueWorker.
 */

namespace Drupal\tpc_userpoints_ext\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\user\Entity\User;

/**
 * Processes tasks for TPC Userpoints Extension module.
 *
 * @QueueWorker(
 *   id = "tpc_userpoints_ext_queue",
 *   title = @Translation("TPC Userpoints Queue Worker"),
 *   cron = {"time" = 30}
 * )
 */

class TPCUserpointsQueueWorker extends QueueWorkerBase {
  
  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    $uid = $item->uid;
    $rank = $item->rank;
    
    $user = User::load($uid);
    $curRank = $user->field_user_ytd_rank->value;
    
    if ($curRank != $rank) {
      $user->set('field_user_ytd_rank', $rank);
      $user->save();
    }
  }
}