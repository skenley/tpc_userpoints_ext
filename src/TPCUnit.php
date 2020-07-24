<?php

namespace Drupal\tpc_userpoints_ext;

class TPCUnit {
  
  /**
   * Converts a TPC apartment unit number (string) to a numerical
   * value.
   */
  static public function unitNumberToDecimal($unitNum) {
    
    $matches = '';
    $tmpNum = str_replace('-', '', $unitNum);
    
    preg_match('/[.*[:alpha:].*]/', $tmpNum, $matches);
    
    if(sizeof($matches) > 0) {
      
      $match = $matches[0];
      $returnNum = strval(ord($match)) . preg_replace('/[.*[:alpha:].*]/', 
        '', $tmpNum);
      return intval($returnNum);
      
    }
    
    return intval($tmpNum);
    
  }
  
}