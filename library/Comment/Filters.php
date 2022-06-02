<?php

namespace Municipio\Comment;

/**
 * Class Filters
 * @package Municipio\Comment
 */
class Filters
{
    /**
     * Filters constructor.
     */
    public function __construct()
    {
        add_filter('comment_text', array($this, 'stripTags'), 10, 2);
    }

    /**
     * Strip html from comment
     * @param $comment_text
     * @param $comment
     * @return array
     */
    public function stripTags($commentText, $comment)
    {
        $allowedTags = array(
            "<h1>", "<h2>", "<h3>", "<h4>",
            "<strong>","<b>",
            "<br>", "<hr>",
            "<em>",
            "<ol>","<ul>","<li>",
            "<p>", "<span>", "<a>", "<img>",
            "<del>", "<ins>",
            "<blockquote>"
        );

        $allowedAttributes = array(
            "href",
            "class",
            "rel",
            "id",
            "src"
        );

        return \Municipio\Helper\Html::stripTagsAndAtts(
            $commentText,
            $allowedTags,
            $allowedAttributes
        );
    }
}
