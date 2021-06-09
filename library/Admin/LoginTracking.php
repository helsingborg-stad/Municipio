<?php

namespace Municipio\Admin;

class LoginTracking
{
  public function __construct() {

    //Create login timestamp
    add_action('wp_login', array($this, 'setTimeStamp'), 20, 2);

    //Add timestamp column
    add_filter('manage_users_columns', array($this, 'addColumn'));
    add_filter('manage_users_custom_column', array($this, 'addColumnData'), 10, 3);

    //Add sorting ability
    add_filter( 'manage_users_sortable_columns', array($this, 'addSortableColumn'));
    add_action( 'pre_get_users', array($this, 'performColumnSort'));
  }

  public function setTimeStamp($user_login, $user) {
    update_user_meta($user->ID, 'last_login', time());
  }

  public function addColumn($columns) {
    
    if(!is_array($columns)) {
      return $columns; 
    }
    
    //add
    $columns['last_login'] = __('Last Login', 'municipio');

    //Retuern updated
    return $columns;
  }

  public function addColumnData($output, $columnId, $userId){
 
    if($columnId == 'last_login') {
      $lastLogin  = get_user_meta($userId, 'last_login', true);
      $output     = $lastLogin ? date('Y-m-d H:i:s', $lastLogin ) : '-';
    }
   
    return $output;
  }

  public function addSortableColumn($columns) {
    return wp_parse_args( array(
       'last_login' => 'last_login'
    ), $columns);
  }
   
  public function performColumnSort($query) {
   
    if(!is_admin()) {
      return $query;
    }
   
    $screen = get_current_screen();
   
    if(isset($screen->id) && $screen->id !== 'users') {
      return $query;
    }
   
    if(isset($_GET['orderby']) && $_GET['orderby'] == 'last_login') {
      $query->query_vars['meta_key']  = 'last_login';
      $query->query_vars['orderby']   = 'meta_value';
    }
   
    return $query;
  }
}