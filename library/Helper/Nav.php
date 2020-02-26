<?php 

  namespace Municipio\Helper;

  /**
  * TODO: CHANGE NAME OF THIS CLASS
  * Navigation items
  * @package Municipio\Theme
  */

  class Nav
  {

    private static $db;
    public static $items; 

    public static function getTopLevel() {

      self::globalToLocal('wpdb', 'db'); 

      return self::convertItemsToArray(
        self::complementObjects(
          self::getItems()
        )
      ); 
    }

    public static function getNested() {

      self::globalToLocal('wpdb', 'db'); 

      return self::convertItemsToArray(
        self::complementObjects(
          self::getItems()
        )
      ); 
    }

    // TODO: Not this, fix fix fix! 
    private static function convertItemsToArray($objects) {
      return json_decode(json_encode($objects), true); 
    }

    private static function complementObjects($objects) {
      
      if(is_array($objects) && !empty($objects)) {
        foreach($objects as &$object) {
          $object = self::appendHref($object); 
          $object = self::customTitle($object); 
          $object = self::transformObject($object);
        }
      }

      return $objects; 
    }

    /**
     * Add post data on post object
     * 
     * @param   object   $postObject    The post object
     * @param   object   $appendFields  Data to append on object
     * 
     * @return  object   $postObject    The post object, with appended data
     */
    public static function appendHref($object, $leavename = false)
    {
        if(!is_a($object, 'stdClass')) {
          return new \WP_Error("Append permalink object must recive a stdClass."); 
        }

        $object->href = get_permalink($object->ID, $leavename);

        return $object; 
    }

    /**
     * Add post data on post object
     * 
     * @param   object   $postObject    The post object
     * @param   object   $appendFields  Data to append on object
     * 
     * @return  object   $postObject    The post object, with appended data
     */
    public static function transformObject($object)
    {
        if(!is_a($object, 'stdClass')) {
          return new \WP_Error("Transform object object must recive a stdClass."); 
        }

        $object->label = $object->post_title;

        return $object; 
    }

    /**
     * Get pages/posts 
     * 
     * @param   integer  $parent    Post parent
     * @param   string   $postType  The post type to query
     * 
     * @return  array               Array of post id:s, post_titles and post_parent
     */
    private static function getItems($parent = 0, $postType = 'page') {

      //Check if if valid post type string
      if($postType != 'all' && !is_array($postType) && !post_type_exists($postType)) {
        return new \WP_Error("Could not get navigation menu for " . $postType . "since it dosen't exist."); 
      }

      //Check if if valid post type array
      if(is_array($postType)) {
        foreach($postType as $item) {
          if(!post_type_exists($item)) {
            return new \WP_Error("Could not get navigation menu for " . $item . "since it dosen't exist."); 
          }
        }
      }

      //Handle post type cases
      if($postType == 'all') {
        $postTypeSQL = "post_type IN(" . implode(", ", get_post_types(['public' => true])) . ")"; 
      } elseif(is_array($postType)) {
        $postTypeSQL = "post_type IN(" . implode(", ", $postType ) . ")"; 
      } else {
        $postTypeSQL = "post_type = '" . $postType . "'"; 
      }

      //Run query TODO: Prepare Query
      return self::$db->get_results("
        SELECT ID, post_title, post_parent 
        FROM " . self::$db->posts . " 
        WHERE post_parent = '" . $parent . "'
        AND " . $postTypeSQL . "
        AND ID NOT IN(" . implode(", ", self::getHiddenPostIds()) . ")
        ORDER BY menu_order ASC 
        LIMIT 99
      ");
    }

    /**
     * Get a list of hidden post id's
     * 
     * Optimzing: We are getting all meta keys since it's the 
     * fastest way of doing this due to missing indexes in database. 
     * 
     * This is a calculated risk that should be caught 
     * by the object cache. Tests have been made to enshure
     * good performance. 
     * 
     * @param string $metaKey The meta key to get data from
     * 
     * @return array
     */
    public static function getHiddenPostIds(string $metaKey = "hide_in_menu") : array
    {

      //Get meta TODO: Prepare Query
      $result = (array) self::$db->get_results("
        SELECT post_id, meta_value 
        FROM ". self::$db->postmeta ." 
        WHERE meta_key = '$metaKey'
      "); 

      //Declare result
      $hiddenPages = []; 

      //Add visible page ids
      if(is_array($result) && !empty($result)) {
        foreach($result as $item) {
          if($item->meta_value != "1") {
            continue; 
          }
          $hiddenPages[] = $item->post_id; 
        }
      }

      return $hiddenPages; 
    }

    /**
     * Get a list of custom page titles
     * 
     * Optimzing: We are getting all meta keys since it's the 
     * fastest way of doing this due to missing indexes in database. 
     * 
     * This is a calculated risk that should be caught 
     * by the object cache. Tests have been made to enshure
     * good performance. 
     * 
     * @param string $metaKey The meta key to get data from
     * 
     * @return array
     */
    public static function getMenuTitle(string $metaKey = "custom_menu_title") : array
    {

      //Get meta TODO: Prepare Query
      $result = (array) self::$db->get_results("
        SELECT post_id, meta_value 
        FROM ". self::$db->postmeta ." 
        WHERE meta_key = '$metaKey'
        AND meta_value != ''
      "); 

      //Declare result
      $pageTitles = []; 

      //Add visible page ids
      if(is_array($result) && !empty($result)) {
        foreach($result as $result) {
          if(empty($result->meta_value)) {
            continue; 
          }
          $pageTitles[$result->post_id] = $result->meta_value; 
        }
      }

      return $pageTitles; 
    }


    /**
     * Replace native title with custom menu name
     * 
     * @param object $object
     * 
     * @return object
     */
    public static function customTitle($object) : object
    {

      $customTitles = self::getMenuTitle(); 
      
      if(isset($customTitles[$object->ID])) {
        $object->post_title = $customTitles[$object->ID]; 
      }

      return $object; 
    }

    /**
     * Creates a local copy of the global instance
     * The target var should be defined in class header as private or public
     * 
     * @param string $global The name of global varable that should be made local
     * @param string $local Handle the global with the name of this string locally
     * 
     * @return void
     */
    public static function globalToLocal($global, $local = null)
    {
      global $$global;
      if (is_null($local)) {
          self::$$global = $$global;
      } else {
          self::$$local = $$global;
      }
    }

  }