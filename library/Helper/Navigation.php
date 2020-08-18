<?php 

namespace Municipio\Helper;

/**
* Navigation items
*
* @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
* @since    3.0.0
* @package  Municipio\Theme
*/

class Navigation
{
  private static $db;
  private static $postId = null;
  private static $cache = []; 

  /**
   * Get nested array representing page structure
   * 
   * @param   array     $postId             The current post id
   * 
   * @return  array                         Nested page array
   */
  public static function getNested($postId) : array
  {

    //Store current post id
    if(is_null(self::$postId)) {
      self::$postId = $postId; 
    }

    //Create local instance of wpdb
    self::globalToLocal('wpdb', 'db');

    //Get all ancestors
    $parents = self::getAncestors($postId);

    //Get all parents
    $result = self::getItems($parents); 
    
    //Format response 
    $result = self::complementObjects($result);

    //Return done
    return $result; 
  }

  public static function getPostChildren($postId) : array
  {

    //Store current post id
    if(is_null(self::$postId)) {
      self::$postId = $postId; 
    }

    //Create local instance of wpdb
    self::globalToLocal('wpdb', 'db');

    //Get all parents
    $result = self::getItems($postId); 

    //Format response 
    $result = self::complementObjects($result);

    //Add support to page for posttype
    $result = self::appendPageForPostTypeItems($result); 
    
    //Return done
    return $result; 
  }

  /**
   * Check if a post has children. If this is the current post, 
   * fetch the actual children array. 
   * 
   * @param   array   $postId    The post id
   * 
   * @return  array              Flat array with parents
   */
  private static function hasChildren(array $array) : array
  {  

    if($array['ID'] == self::$postId) {
      $children = self::getItems($array['ID']); 
    } else {
      $children = self::getChildren($array['ID']);
      
    }

    //If null, no children
    if(is_array($children)) {
      $array['children'] = self::complementObjects($children);
    } else {
      $array['children'] = is_null($children) ? false : true; 
    }

    //Return result
    return $array; 
  }

  /**
   * Get posts children
   * 
   * @param   array   $postId    The post id
   * 
   * @return  array              Array of childrens
   */
  public static function getChildren($postId)
  {  

    $children = self::$db->get_var(
      self::$db->prepare("
        SELECT ID 
        FROM " . self::$db->posts . " 
        WHERE post_parent = %d 
        AND post_status = 'publish'
        AND ID NOT IN(" . implode(", ", self::getHiddenPostIds()) . ")
        LIMIT 1
      ", $postId)
    );
    
    if(is_null($children)) {
      return $children;
    } else {
      return true;
    }
    
  }

  /**
   * Recusivly traverse flat array and make a nested variant
   * 
   * @param   array   $postId    The current post id
   * 
   * @return  array              Flat array with parents
   */
  private static function getAncestors(int $postId) : array
  { 
    //Fetch from cache
    if(isset(self::$cache['ancestors'])) {
      return self::$cache['ancestors']; 
    }

    return self::$cache['ancestors'] = array_merge([0], array_reverse(get_post_ancestors($postId)));
  }

  /**
   * Recusivly traverse flat array and make a nested variant
   * 
   * @param   array   $elements    A list of pages
   * @param   integer $parentId    Parent id
   * 
   * @return  array               Nested array representing page structure
   */
  private static function buildTree(array $elements, $parentId = 0) : array 
  {

    $branch = array();

    if(is_array($elements) && !empty($elements)) {
      foreach ($elements as $element) {
        if ($element['post_parent'] == $parentId) {

          $children = self::buildTree($elements, $element['id']);

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
   * @param   integer|array  $parent    Post parent
   * @param   string|array   $postType  The post type to query
   * 
   * @return  array               Array of post id:s, post_titles and post_parent
   */
  private static function getItems($parent = 0, $postType = 'page') : array 
  {

    //Check if if valid post type string
    if($postType != 'all' && !is_array($postType) && !post_type_exists($postType)) {
      return new \WP_Error("Could not get navigation menu for " . $postType . " since it dosen't exist."); 
    }

    //Check if if valid post type array
    if(is_array($postType)) {
      foreach($postType as $item) {
        if(!post_type_exists($item)) {
          return new \WP_Error("Could not get navigation menu for " . $item . " since it dosen't exist."); 
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

    //Support multi level query
    if(!is_array($parent)) {
      $parent = [$parent]; 
    }
    $parent = implode(", ", $parent); 

    //Run query
    return self::$db->get_results("
      SELECT ID, post_title, post_parent 
      FROM " . self::$db->posts . " 
      WHERE post_parent IN(" . $parent . ")
      AND " . $postTypeSQL . "
      AND ID NOT IN(" . implode(", ", self::getHiddenPostIds()) . ")
      AND post_status='publish'
      ORDER BY post_title, menu_order ASC 
      LIMIT 500
    ", ARRAY_A);
  }
  

  /**
   * Calculate add add data to array
   * 
   * @param   array    $objects     The post array
   * 
   * @return  array    $objects     The post array, with appended data
   */
  private static function complementObjects(array $objects) {
    
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
  private static function appendIsAncestorPost(array $array) : array
  {
      if(in_array($array['ID'], self::getAncestors(self::$postId))) {
        $array['ancestor'] = true; 
      } else {
        $array['ancestor'] = false; 
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
  private static function appendIsCurrentPost(array $array) : array
  {
      if($array['ID'] == self::$postId) {
        $array['active'] = true; 
      } else {
        $array['active'] = false; 
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
  private static function appendHref(array $array, bool $leavename = false) : array
  {
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
  private static function transformObject(array $array) : array
  {
      //Move post_title to label key
      $array['label'] = $array['post_title'];
      $array['id'] = (int) $array['ID'];
      $array['post_parent'] = (int) $array['post_parent'];
      
      //Unset data not needed
      unset($array['post_title']); 
      unset($array['ID']); 

      //Sort & return
      return array_merge(
        array(
          'id' => null,
          'post_parent' => null,
          'active' => null,
          'ancestor' => null,
          'label' => null,
          'href' => null,
          'children' => null
        ), $array
      ); 
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

    //Get cached result
    if(isset(self::$cache['getHiddenPostIds'])) {
      return self::$cache['getHiddenPostIds']; 
    }

    //Get meta
    $result = (array) self::$db->get_results(
      self::$db->prepare("
        SELECT post_id, meta_value 
        FROM ". self::$db->postmeta ." 
        WHERE meta_key = %s
      ", $metaKey)
    ); 

    //Declare result
    $hiddenPages = [PHP_INT_MAX]; 

    //Add visible page ids
    if(is_array($result) && !empty($result)) {
      foreach($result as $item) {
        if($item->meta_value != "1") {
          continue; 
        }
        $hiddenPages[] = $item->post_id; 
      }
    }

    return self::$cache['getHiddenPostIds'] = $hiddenPages; 
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

    //Get cached result
    if(isset(self::$cache['getMenuTitle'])) {
      return self::$cache['getMenuTitle']; 
    }

    //Get meta
    $result = (array) self::$db->get_results(
      self::$db->prepare("
        SELECT post_id, meta_value 
        FROM ". self::$db->postmeta ." 
        WHERE meta_key = %s
        AND meta_value != ''
      ", $metaKey)
    ); 

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

    return self::$cache['getMenuTitle'] = $pageTitles; 
  }

  /**
   * Replace native title with custom menu name
   * 
   * @param array $array
   * 
   * @return object
   */
  private static function customTitle(array $array) : array
  {
    $customTitles = self::getMenuTitle(); 

    //Get custom title
    if(isset($customTitles[$array['ID']])) {
      $array['post_title'] = $customTitles[$array['ID']]; 
    }

    //Replace empty titles
    if($array['post_title'] == "") {
      $array['post_title'] = __("Untitled page", 'municipio'); 
    }

    return $array; 
  }

  /**
   * Get WordPress menu items (from default menu management)
   *
   * @param string $menu The menu id to get
   * @return bool|array
   */
  public static function getMenuItems(string $menu, int $pageId = null, bool $fallbackToPageTree = false, bool $includeTopLevel = true)
  {

      //Check for existing wp menu
      if (has_nav_menu($menu)) {
          
          $menuItems = wp_get_nav_menu_items(get_nav_menu_locations()[$menu]); 

          if(is_array($menuItems) && !empty($menuItems)) {

            $result = []; //Storage of result

            foreach ($menuItems as $item) {
              $result[$item->ID] = [
                  'id' => $item->ID,
                  'label' => $item->title,
                  'href' => $item->url,
                  'children' => false,
                  'post_parent' => $item->menu_item_parent
              ];
            }
          } else {
            $result = [];
          }
      } else {
        //Get page tree
        if($fallbackToPageTree === true && is_numeric($pageId)) {
          $result =  self::getNested($pageId); 
        } else {
          $result = [];
        }
      }

      //Filter for appending and removing objects from navgation
      $result = apply_filters('Municipio/Navigation/Items', $result);

      //Create nested array
      if(!empty($result) && is_array($result)) {

        //Add support to page for posttype
        $result = self::appendPageForPostTypeItems($result); 

        //Wheter to include top level or not
        if($includeTopLevel === true) {
          return self::buildTree($result);
        } else {
          return self::removeTopLevel(
            self::buildTree($result)
          );
        }
      }

      return false;
  }

  /**
   * Removes top level items
   *
   * @param   array   $result    The unfiltered result set
   * 
   * @return  array   $result    The filtered result set (without top level)
   */
  public static function removeTopLevel(array $result) : array {
    foreach($result as $key => $item) {
      
      $id = array_filter(self::getAncestors(self::$postId)); 

      if(!empty($id) && $val = array_shift($id)) {
        $id = $val;
      } else {
        $id = self::$postId; 
      }

      if($item['id'] == $id) {
        return $item['children']; 
      }
    }
    return []; 
  }

  /**
   * BreadCrumbData
   * Fetching data for breadcrumbs
   * @return array|void
   * @throws \Exception
   */
  public static function getBreadcrumbItems()
  {
      global $post;

      if (!is_a($post, 'WP_Post')) {
          return;
      }

      if (!is_front_page()) {

          $post_type = get_post_type_object($post->post_type);
          $pageData = array();

          $id = \Municipio\Helper\Hash::mkUniqueId();

          $pageData[$id]['label'] = __('Home');
          $pageData[$id]['href'] = get_home_url();
          $pageData[$id]['current'] = false;
          $pageData[$id]['icon'] = "home"; 

          if (is_single() && $post_type->has_archive) {

              $id = \Municipio\Helper\Hash::mkUniqueId();
              $pageData[$id]['label'] = $post_type->label;

              $pageData[$id]['href'] = (is_string($post_type->has_archive))
                  ? get_permalink(get_page_by_path($post_type->has_archive))
                  : get_post_type_archive_link($post_type->name);

              $pageData[$id]['current'] = false;
          }

          if (is_page() || (is_single() && $post_type->hierarchical === true)) {
              if ($post->post_parent) {

                  $ancestors = array_reverse(get_post_ancestors($post->ID));
                  $title = get_the_title();

                  foreach ($ancestors as $ancestor) {
                      if (get_post_status($ancestor) !== 'private') {
                          $id = \Municipio\Helper\Hash::mkUniqueId();
                          $pageData[$id]['label'] = get_the_title($ancestor);
                          $pageData[$id]['href'] = get_permalink($ancestor);
                          $pageData[$id]['current'] = false;
                      }
                  }

                  $id = \Municipio\Helper\Hash::mkUniqueId();
                  $pageData[$id]['label'] = $title;
                  $pageData[$id]['href'] = '';
                  $pageData[$id]['current'] = true;

              } else {
                  $id = \Municipio\Helper\Hash::mkUniqueId();
                  $pageData[$id]['label'] = get_the_title();
                  $pageData[$id]['href'] = '';
                  $pageData[$id]['current'] = true;
              }

          } else {

              if (is_home()) {
                  $title = single_post_title("", false);
              } elseif (is_tax()) {
                  $title = single_cat_title(null, false);
              } elseif (is_category() && $title = get_the_category()) {
                  $title = $title[0]->name;
              } elseif (is_archive()) {
                  $title = post_type_archive_title(null, false);
              } else {
                  $title = get_the_title();
              }

              $id = \Municipio\Helper\Hash::mkUniqueId();
              $pageData[$id]['label'] = $title;
              $pageData[$id]['href'] = '';
              $pageData[$id]['current'] = false;
          }

          return apply_filters('Municipio/Breadcrumbs/Items', $pageData, get_queried_object());
      }
  }

  /**
   * Get all post id's mapped as a post type container. 
   *
   * @return array
   */
  public static function getPageForPostTypeIds() : array {

    //Get cached result
    if(isset(self::$cache['pageForPostType'])) {
      return self::$cache['pageForPostType']; 
    }

    //Declare results array 
    $result = array();

    //Only supported for hierarchical
    $postTypes = get_post_types([
      'public' => true, 
      'hierarchical' => true
    ]); 

    //Check for results 
    if(is_countable($postTypes)) {
      foreach($postTypes as $postType) {
        
        //Fetch mapping ID
        $postId = get_option('page_for_' . $postType, true);

        //Validate mapping ID
        if(is_numeric($postId)) {
          $result[$postId] = $postType; 
        }
      }
    }

    return $cache['pageForPostType'] = $result; 
  }

  /**
   * Appends items from page for post type menu mapping plugin
   *
   * @param   array $result   The page structure
   * @param   bool  $getItems Boolean indicating wheter to fetch childs, or just a indicator of childs. 
   * @return  array $result   Menu with appended pfp items. 
   */
  public static function appendPageForPostTypeItems($result, $getItems = true) {

    if(is_countable($result)) {
      foreach($result as $key => $item) {
        $subset = [];
        
        $pageForPostTypeIds = self::getPageForPostTypeIds(); 
        
        if(is_array($pageForPostTypeIds) && array_key_exists($item['id'], $pageForPostTypeIds)) {
          
          $result[$key]['children'] = true;

          if($getItems === true) {
            
            $subset = self::getItems(0, $pageForPostTypeIds[$item['id']]); 
            
            if(is_countable($subset)) {
              
              //Update post parent, if top level before. 
              foreach($subset as $subKey => $subItem) {
                if($subset[$subKey]['post_parent'] == 0) {
                  $subset[$subKey]['post_parent'] = $item['id'];
                }
              }

              //Restructure result 
              $subset = self::complementObjects($subset); 
            }

            //Merge origon menu
            $result = array_merge($result, (array) $subset);

          }
        }
      }
    }

    return $result;
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