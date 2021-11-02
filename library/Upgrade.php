<?php 

namespace Municipio;

/**
 * Class App
 * @package Municipio
 */
class Upgrade
{
  private $dbVersion = 1; //The db version we want to achive 
  private $dbVersionKey = 'municipio_db_version'; 
  private $db; 

  /**
   * App constructor.
   */
  public function __construct()
  {
    add_action('init', array($this, 'initUpgrade')); 
  }

  /**
   * Upgrade database,
   * when you want to upgrade database, 
   * create a new function and increase 
   * $this->dbVersion. 
   * 
   * Method inspiration from WordPress Core. 
   *
   * @return boolean
   */
  private function v_1($db) : bool {

    //update code here

    return true; 
  }

  /**
   * Run upgrade functions
   *
   * @return void
   */
  public function initUpgrade () {

    $currentDbVersion = is_numeric(get_option($this->dbVersionKey)) ? (int) get_option($this->dbVersionKey) : 1; 

    if($this->dbVersion != $currentDbVersion) {

      if(!is_numeric($this->dbVersion)) {
        wp_die(__('To be installed database version must be a number.', 'municipio')); 
      }

      if(!is_numeric($currentDbVersion)) {
        wp_die(__('Current database version must be a number.', 'municipio')); 
      }

      if($currentDbVersion > $this->dbVersion) {
        wp_die(__('Database cannot be lower than currently installed (cannot downgrade).', 'municipio')); 
      }

      //Fetch global wpdb object, save to $db
      $this->globalToLocal('wpdb', 'db'); 

      //Run upgrade(s)
      while($currentDbVersion <= $this->dbVersion) {
        $currentDbVersion++; 
        $funcName = 'v_' . (string) $currentDbVersion; 
        if(method_exists($this, $funcName)) {
          if($this->{$funcName}($this->db)) {
            update_option($this->dbVersionKey, (int) $currentDbVersion); 
          }
        }
      }
    }
  }

  /**
   * Creates a local copy of the global instance
   * The target var should be defined in class header as private or public
   * @param string $global The name of global varable that should be made local
   * @param string $local Handle the global with the name of this string locally
   * @return void
   */
  private function globalToLocal($global, $local = null)
  {
      global $$global;

      if(is_null($$global)) {
          return false;
      }

      if (is_null($local)) {
          $this->$global = $$global;
      } else {
          $this->$local = $$global;
      }

      return true;
  }
}