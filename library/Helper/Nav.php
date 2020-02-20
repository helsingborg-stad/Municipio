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

    public function __construct($postType, $level) {

      //Creates a local instance of wbdb
      self::globalToLocal('wpdb', 'db'); 

      if($level == "top") {
        return self::complementObjects(
          self::getItems()
        ); 
      }
    }

    private static function complementObjects($objects) {
      foreach($objects as &$object) {
       
        $object = self::appendPermalink($object); 
        var_dump($object); 
        
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
    public static function appendPermalink($object, $leavename = true)
    {
        if(!is_a($object, 'stdClass')) {
          return new \WP_Error("Append permalink object must recive a stdClass."); 
        }

        $object->permalink = get_permalink($object, $leavename);

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

      //Handle post type cases
      if($postType == 'all') {
        $postTypeSQL = "post_type IN(" . implode(", ", get_post_types(['public' => true])) . ")"; 
      } elseif(is_array($postType)) {
        $postTypeSQL = "post_type IN(" . implode(", ", $postType ) . ")"; 
      } else {
        $postTypeSQL = "post_type = '" . $postType . "'"; 
      }

      //Run query
      return self::$db->get_results("
        SELECT ID, post_title, post_parent 
        FROM " . self::$db->posts . " 
        WHERE post_parent = '" . $parent . "'
        AND " . $postTypeSQL . "
        ORDER BY menu_order ASC 
        LIMIT 99
      ");
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