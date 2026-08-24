<?php

declare(strict_types=1);


namespace Municipio\Search\Index;

use \Municipio\Search\Index\Helper\Id as Id;
use \Municipio\Search\Index\Helper\Index as Instance;
use \Municipio\Search\Index\Helper\Indexable as Indexable;
use \Municipio\Search\Index\Helper\Log as Log;
use Municipio\Search\Index\Provider\AbstractProvider;

class Index
{
    //Priority on hooks
    private static $_priority = 999;

    //Keys for keeping track of partial records
    private static $partialObjectDistinctKey = 'partial_object_distinct_key';
    private static $partialObjectTotalAmount = 'partial_object_total_amount';

    //Maximum size of record
    private static $_nearMaxLimitSize = 9999;

    // Post Object keys
    private $wpPostObjectKeys = [];

    private AbstractProvider $searchDB;

    /**
     * Constructor, runs code on wordpress hooks.
     */
    public function __construct($hookActions = true, null|AbstractProvider $searchDB = null)
    {
        $this->wpPostObjectKeys = array_keys(get_object_vars(new \WP_Post((object) [])));
        $this->searchDB = $searchDB ?? Instance::getIndex();

        //Test bailout
        if ($hookActions === false) {
            return;
        }

        //Add & update
        add_action('save_post', [$this, 'index'], self::$_priority);

        //Remove
        add_action('delete_post', [$this, 'delete'], self::$_priority);
        add_action('wp_trash_post', [$this, 'delete'], self::$_priority);

        //Bulk action
        add_action('AlgoliaIndex/IndexPostId', [$this, 'index'], self::$_priority, 1);
    }

    /**
     * Delete post from index
     *
     * @param int $postId
     * @return void
     */
    public function delete($postId, $isSplitRecord = false)
    {
        if ($isSplitRecord && is_numeric($isSplitRecord)) {
            //Declarations
            $ids = [];

            //Create all id's
            for ($x = 0; $x <= $isSplitRecord; $x++) {
                $ids[] = self::createChunkId(Id::getId($postId), $x);
            }

            //Delete split records
            if (!empty($ids)) {
                return $this->searchDB->deleteObjects($ids);
            } else {
                Log::error('Could not create array of ids for deletion (splitrecord). Trying to delete single post.');
            }
        }

        //Delete normal records
        return $this->searchDB->deleteObject(Id::getId($postId));
    }

    /**
     * Submit post to index
     *
     * @param int|WP_Post $post
     * @return void
     */
    public function index($post)
    {
        list($post, $postId) = self::getPostAndPostId($post);

        //Check if post should be removed
        $shouldPostBeRemoved = [
            isset($_POST['exclude-from-search']) && $_POST['exclude-from-search'] == 'true',
            get_post_status($post) !== 'publish',
        ];

        if (in_array(true, $shouldPostBeRemoved, strict: true)) {
            if ($isSplitRecord = $this->isSplitRecord($postId)) {
                self::delete($postId, $isSplitRecord);
            } else {
                self::delete($postId);
            }
        }

        //Check if is indexable post
        if (!self::shouldIndex($post)) {
            return;
        }

        //Delete split record (no check if has changed)
        if ($isSplitRecord = $this->isSplitRecord($postId)) {
            self::delete($postId, $isSplitRecord);
        } else {
            //Check if the new post differs from indexed record (not applicable for split records)
            if (!$this->hasChanged($postId)) {
                return;
            }
        }

        //Get post data
        $post = self::getPost($post);

        //Sanity check (convert data)
        $post = _wp_json_sanity_check($post, 10);

        //Esape html entities
        array_walk_recursive($post, function (&$value, $key) {
            if (in_array($key, $this->wpPostObjectKeys, strict: true)) {
                // Converts Int to string (ID)
                if (!is_string($value)) {
                    $value = strval($value);
                }

                $value = htmlentities($value);
            }
        });

        $post = self::utf8ize($post); // UTF-8 Escape

        try {
            //Index post
            if ($this->recordToLarge($post)) {
                $splitRecord = self::splitRecord($post);
                $splitRecord = self::utf8ize($splitRecord);

                if (is_array($splitRecord) && !empty($splitRecord)) {
                    //Catch error here.
                    json_encode($splitRecord, JSON_THROW_ON_ERROR);

                    $this->searchDB->saveObjects($splitRecord, ['objectIDKey' => 'uuid']);
                }
            } else {
                //Catch error here.
                json_encode($post, JSON_THROW_ON_ERROR);

                $this->searchDB->saveObject($post, ['objectIDKey' => 'uuid']);
            }
        } catch (\Exception $e) {
            error_log('Algolia Index: Could not save post. ' . $post['ID']);
        }
    }

    /**
     * Determine if the post should be indexed.
     *
     * @param int|WP_Post $post
     * @return boolean
     */
    private static function shouldIndex($post)
    {
        list($post, $postId) = self::getPostAndPostId($post);

        //Do not index on autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE == true) {
            return false;
        }

        //Do no index revisions
        if (wp_is_post_revision($post)) {
            return false;
        }

        //Check if published post (or any other allowed value)
        if (!in_array(get_post_status($post), Indexable::postStatuses(), strict: true)) {
            return false;
        }

        //Get post type details
        if (!in_array(get_post_type($post), Indexable::postTypes(), strict: true)) {
            return false;
        }

        //Do not index checkbox
        if (get_post_meta($postId, 'exclude_from_search', true)) {
            return false;
        }

        //Anything else
        if (!apply_filters('AlgoliaIndex/ShouldIndex', true, $postId)) {
            return false;
        }

        return true;
    }

    /**
     * Check if record in algolia matches locally stored record.
     *
     * @param int|WP_Post $post
     * @return boolean
     */
    private function hasChanged($post)
    {
        list($post, $postId) = self::getPostAndPostId($post);

        //Make search
        $hits = $this->searchDB->getObjects([Id::getId($postId)]);

        //Get result
        $indexRecord = !empty($hits) ? $hits[0] : null;

        //Get stored record
        $storedRecord = self::getPost($post);

        //Check for null responses, update needed
        if (is_null($indexRecord) || is_null($storedRecord)) {
            return true;
        }

        //Filter out everything that dosen't matter
        $indexRecord = self::streamlineRecord($indexRecord);
        $storedRecord = self::streamlineRecord(self::getPost($postId));

        //Diff posts
        if (serialize($indexRecord) != serialize($storedRecord)) {
            return true; //Post has updates
        }
        return false; //Post has no updates
    }

    /**
     * Streamline record, basicly tells what to use
     * to compare posts for update checking.
     *
     * @param array $record
     * @return array
     */
    private static function streamlineRecord($record)
    {
        //List of fields to compare
        $comparables = apply_filters('AlgoliaIndex/Compare', [
            'ID',
            'post_title',
            'post_excerpt',
            'content',
            'permalink',
            'thumbnail',
        ]);

        //Prepare comparables
        $record = (array) array_intersect_key($record, array_flip($comparables));

        //Sort (resolves different orders)
        array_multisort($record);

        //Send back
        return $record;
    }

    /**
     * Get post by ID
     *
     * @param int|WP_Post $post
     * @return array
     */
    private static function getPost($post)
    {
        list($post, $postId) = self::getPostAndPostId($post);

        if ($post = get_post($post)) {
            /* Tags */
            $taxonomies = get_post_taxonomies($post);
            $tags = [];

            if (is_array($taxonomies) && !empty($taxonomies)) {
                foreach ($taxonomies as $taxonomy) {
                    if ($taxonomy === 'category') { continue; }

$tags = array_merge($tags, array_map(
                            static fn (\WP_Term $term) => $term->name,
                            wp_get_post_terms($postId, $taxonomy),
                        ));
                }
            }

            //Categories
            $categories = array_map(
                static fn (\WP_Term $term) => $term->name,
                wp_get_post_terms($postId, 'category'),
            );

            //Post details
            $result = [
                'uuid' => Id::getId($postId),
                'ID' => $post->ID,
                'post_title' => apply_filters('the_title', $post->post_title),
                'post_excerpt' => self::getTheExcerpt($post),
                'content' => self::stripTags(apply_filters('the_content', $post->post_content)),
                'permalink' => get_permalink($post->ID),
                'post_date' => strtotime($post->post_date),
                'post_date_formatted' => date(get_option('date_format'), strtotime($post->post_date)),
                'post_modified' => strtotime($post->post_modified),
                'thumbnail' => get_the_post_thumbnail_url($post) ? get_the_post_thumbnail_url($post, [480, 270]) : '',
                'thumbnail_alt' => get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true),
                'tags' => $tags,
                'categories' => $categories,
                'algolia_timestamp' => current_time('Y-m-d H:i:s'),
                'post_type' => get_post_type($postId),
                'post_type_name' => get_post_type_labels(get_post_type_object(get_post_type($postId)))->name,
                'top_most_parent' => self::getTopMostParentTitle($postId),
            ];

            //Site
            $result['origin_site'] = get_bloginfo('name');
            $result['origin_site_url'] = get_bloginfo('url');

            //Add blog id
            if (is_multisite()) {
                $result['blog_id'] = get_current_blog_id();
            }

            //Remove multiple spaces
            foreach ($result as $key => $field) {
                if (!(in_array($key, ['post_title', 'post_excerpt', 'content'], strict: true))) { continue; }

$result[$key] = preg_replace('/\s+/', ' ', $field);
            }

            return apply_filters('AlgoliaIndex/Record', $result, $postId);
        }

        return null;
    }

    public static function stripTags($content)
    {
        $removeBodyOfTags = [
            'script',
            'style',
            'noscript',
        ];

        $content = preg_replace(sprintf('/<(%s)\b[^>]*>.*?<\/\1>/is', implode('|', $removeBodyOfTags)), '', $content);

        return strip_tags($content);
    }

    public static function getTheExcerpt($post, int $numberOfWords = 55)
    {
        $excerpt = get_the_excerpt($post);

        if (empty($excerpt) || strlen($excerpt) > 10) {
            $excerpt = !empty($post->post_content) ? $post->post_content : $excerpt;
        }

        $blocks = parse_blocks($excerpt);
        if (is_countable($blocks) && !empty($blocks)) {
            $excerpt = '';
            foreach ($blocks as $block) {
                $excerpt .= render_block($block) . ' ' . PHP_EOL;
            }
        }

        // Sanitizing excerpt
        $excerpt = preg_replace('/\[(.*?)\]/', '', $excerpt);
        $excerpt = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $excerpt);
        $excerpt = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $excerpt);

        return wp_trim_words(strip_tags($excerpt), $numberOfWords, '...');
    }

    /**
     * Check if the record is close to the limit of algolia max record size.
     * This applies for most plans.
     *
     * @param array $record
     * @return void
     */
    private function recordToLarge($record)
    {
        if (
            $this->searchDB->shouldSplitRecord()
            && mb_strlen(serialize((array) $record), '8bit') >= self::$_nearMaxLimitSize
        ) {
            return apply_filters('AlgoliaIndex/RecordToLarge', true);
        }
        return apply_filters('AlgoliaIndex/RecordToLarge', false);
        
    }

    /**
     * Split record in multiple chunks.
     *
     * @param [type] $record
     * @return void
     */
    private static function splitRecord($record)
    {
        //Response storage
        $result = [];

        //Calculation of parts
        $contentSize = mb_strlen($record['content'], '8bit');
        $additionalSize = mb_strlen(serialize(array_diff_key($record, array_flip(['content']))), '8bit');
        $numberOfChunks = (int) ceil($contentSize / (self::$_nearMaxLimitSize - $additionalSize));
        $chunkSize = (int) ceil($contentSize / $numberOfChunks);
        $contentChunks = str_split($record['content'], empty($chunkSize) ? 1 : $chunkSize);

        //Create final object to be indexed
        foreach ($contentChunks as $chunkKey => $chunk) {
            $result[$chunkKey] = array_merge($record, [
                'content' => $chunk,
                self::$partialObjectDistinctKey => $record['uuid'],
                self::$partialObjectTotalAmount => count($contentChunks),
            ]);

            $result[$chunkKey]['uuid'] = self::createChunkId($record['uuid'], $chunkKey);
        }

        //Return chunked (or original record if failed to create chunked record).
        return !empty($result) ? $result : [$record];
    }

    /**
     * Creates a chunk id
     *
     * @param   string  $uuid   The id of the post
     * @param   integer $chunk  The integer for chunk part
     * @return  string          New unique string of item
     */
    private static function createChunkId($uuid, $chunk)
    {
        if ($chunk != 0) {
            return $uuid . '-part-' . $chunk;
        }
        return $uuid;
    }

    /**
     * Check if stored record in algolia is a split record.
     * If a split record is found, the number of parts will be
     * returned.
     *
     * @param int $postId
     * @return boolean / integer
     */
    private function isSplitRecord($postId)
    {
        $response = $this->searchDB->getObjects([Id::getId($postId)]);

        if (
            !empty($response)
            && !empty($response[0])
            && array_key_exists(self::$partialObjectDistinctKey, $response[0])
        ) {
            return $response[0][self::$partialObjectTotalAmount];
        }

        return false;
    }

    /**
     * Get post and post id
     *
     * @param int|WP_Post $post
     * @return array [WP_Post, int] or [int, int] depending on input.
     */
    private static function getPostAndPostId($post)
    {
        $postId = $post;

        if (is_a($post, 'WP_Post')) {
            $postId = $post->ID;
        }

        return [$post, $postId];
    }

    /**
     * Convert data to utf-8
     *
     * @param mixed $data
     * @return mixed
     */
    public static function utf8ize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::utf8ize($value);
            }
        } else if (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = self::utf8ize($value);
            }
        } else if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8');
        }
        return $data;
    }

    /**
     * Get the top most parent title of a post
     * @param int $postId
     * @return string
     */
    private static function getTopMostParentTitle($postId)
    {
        $parentId = wp_get_post_parent_id($postId);
        $topMostParentId = $postId;

        while ($parentId) {
            $topMostParentId = $parentId;
            $parentId = wp_get_post_parent_id($topMostParentId);
        }

        if ($topMostParentId && $topMostParentId != $postId) {
            return get_the_title($topMostParentId);
        }

        return get_the_title($postId);
    }
}
