<?php 

namespace Municipio\Helper;

/**
* TODO: CHANGE NAME OF THIS CLASS
* Navigation items
*
* @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
* @since    3.0.0
* @package  Municipio\Theme
*/

class Nav
{
  private static $db;
  private static $postId = null;

  /**
   * Get flat array with top level items
   * 
   * @param   array     $postId             The current post id
   * @depends           getNested           Must invoke get nested function
   * 
   * @return  array                         Flat top level page array
   */
  public static function getTopLevel($postId) : array 
  {
    //Get top level
    return self::getNested($postId, true, 1); 
  }

  /**
   * Get nested array representing page structure
   * 
   * @param   array     $postId             The current post id
   * @param   bool      $includeTopLevel    Include top level in response
   * @param   int|bool  $maxLevels          The maximum levels to traverse
   * 
   * @return  array                         Nested page array
   */
  public static function getNested($postId, $includeTopLevel = true, $maxLevels = false) : array
  {

    //Store current post id
    if(is_null(self::$postId)) {
      self::$postId = $postId; 
    }

    //Create local instance of wpdb
    self::globalToLocal('wpdb', 'db');

    //Get all ancestors, append top level if true
    if($includeTopLevel === true) {
      $parents = array_merge([0], (array) self::getAncestors($postId));
    } else {
      $parents = array_merge((array) self::getAncestors($postId));
    }

    //Max level limiter
    if($maxLevels != false && is_numeric($maxLevels)) {
      $parents = array_slice($parents, 0, $maxLevels);
    }

    //Get all parents
    $result = self::getItems($parents); 

    //Format response 
    $result = self::complementObjects($result);

    //Restructure array to get tree, if multi level
    if($maxLevels === false || (is_numeric($maxLevels) && $maxLevels != 1)) {
      $result = self::buildTree($result);
    }

    //Return done
    return $result; 
  }

  /**
   * Check if a post has children
   * 
   * @param   array   $postId    The post id
   * 
   * @return  array              Flat array with parents
   */
  private static function hasChildren($array) : array
  {  
    if(!is_array($array)) {
      return new \WP_Error("Append permalink object must recive an array."); 
    }

    $children = self::$db->get_results("
      SELECT ID
      FROM " . self::$db->posts . " 
      WHERE post_parent = '". $array['ID'] . "'
      LIMIT 1
    ", ARRAY_A);

    if(!empty($children)) {
      $array['children'] = true; 
    } else {
      $array['children'] = false; 
    }

    return $array; 
  }

  /**
   * Recusivly traverse flat array and make a nested variant
   * 
   * @param   array   $postId    The current post id
   * 
   * @return  array              Flat array with parents
   */
  private static function getAncestors($postId) : array
  {  
    return array_reverse(get_post_ancestors($postId));
  }

  /**
   * Recusivly traverse flat array and make a nested variant
   * 
   * @param   array   $elements    A list of pages
   * @param   integer $parentId    Parent id
   * 
   * @return  array               Nested array representing page structure
   */
  private static function buildTree(array $elements, int $parentId = 0) : array 
  {

    $branch = array();

    if(is_array($elements) && !empty($elements)) {
      foreach ($elements as $element) {
        if ($element['post_parent'] == $parentId) {

          $children = self::buildTree($elements, $element['ID']);

          if ($children) {
              $element['children'] = $children;
          }

          $branch[] = $element;
        }
      }
    }

    return $branch;
  }

  /**
   * Get pages/posts 
   * 
   * @param   integer  $parent    Post parent
   * @param   string   $postType  The post type to query
   * 
   * @return  array               Array of post id:s, post_titles and post_parent
   */
  private static function getItems($parent = 0, $postType = 'page') : array 
  {

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

    //Default to parent = 0
    if(empty($parent)) {
      $parent = 0; 
    }

    //Support multi level query
    if(!is_array($parent)) {
      $parent = [$parent]; 
    }
    $parent = implode(", ", $parent); 

    //Run query TODO: Prepare Query
    return self::$db->get_results("
      SELECT ID, post_title, post_parent 
      FROM " . self::$db->posts . " 
      WHERE post_parent IN(" . $parent . ")
      AND " . $postTypeSQL . "
      AND ID NOT IN(" . implode(", ", self::getHiddenPostIds()) . ")
      AND post_status='publish'
      ORDER BY menu_order ASC 
      LIMIT 500
    ", ARRAY_A);
  }

  /**
   * Calculate add add data to array
   * 
   * @param   object   $objects     The post array
   * 
   * @return  array    $objects     The post array, with appended data
   */
  private static function complementObjects($objects) {
    
    if(is_array($objects) && !empty($objects)) {
      foreach($objects as $key => $item) {
        $objects[$key] = self::transformObject(
          self::hasChildren(
            self::appendIsAncestorPost(
              self::appendIsCurrentPost(
                self::customTitle(
                  self::appendHref($item)
                )
              )
            )
          )
        );
      }
    }

    return $objects; 
  }

  /**
   * Add post is ancestor data on post array
   * 
   * @param   object   $array         The post array
   * 
   * @return  array    $postArray     The post array, with appended data
   */
  private static function appendIsAncestorPost($array) : array
  {
      if(!is_array($array)) {
        return new \WP_Error("Append permalink object must recive an array."); 
      }

      if(in_array($array['ID'], self::getAncestors(self::$postId))) {
        $array['ancestor'] = true; 
      }

      return $array; 
  }

  /**
   * Add post is current data on post array
   * 
   * @param   object   $array         The post array
   * 
   * @return  array    $postArray     The post array, with appended data
   */
  private static function appendIsCurrentPost($array) : array
  {
      if(!is_array($array)) {
        return new \WP_Error("Append permalink object must recive an array."); 
      }

      if($array['ID'] == self::$postId) {
        $array['active'] = true; 
      }
      
      return $array; 
  }

  /**
   * Add post href data on post array
   * 
   * @param   object   $array         The post array
   * @param   boolean  $leavename     Leave name wp default param
   * 
   * @return  array    $postArray     The post array, with appended data
   */
  private static function appendHref($array, $leavename = false) : array
  {
      if(!is_array($array)) {
        return new \WP_Error("Append permalink object must recive an array."); 
      }

      $array['href'] = get_permalink($array['ID'], $leavename);

      return $array; 
  }

  /**
   * Add post data on post array
   * 
   * @param   array   $array  The post array
   * 
   * @return  array   $array  The post array, with appended data
   */
  private static function transformObject($array) : array
  {
      if(!is_array($array)) {
        return new \WP_Error("Transform object object must recive an array."); 
      }

      //Move post_title to label key
      $array['label'] = $array['post_title'];
      $array['id'] = $array['ID'];
      
      //Unset data not needed
      unset($array['post_title']); 

      return $array; 
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
  private static function getHiddenPostIds(string $metaKey = "hide_in_menu") : array
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
  private static function getMenuTitle(string $metaKey = "custom_menu_title") : array
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
   * @param array $array
   * 
   * @return object
   */
  private static function customTitle($array) : array
  {
    $customTitles = self::getMenuTitle(); 

    if(isset($customTitles[$array['ID']])) {
      $array['post_title'] = $customTitles[$array['ID']]; 
    }

    return $array; 
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
  private static function globalToLocal($global, $local = null)
  {
    global $$global;
    if (is_null($local)) {
        self::$$global = $$global;
    } else {
        self::$$local = $$global;
    }
  }

}