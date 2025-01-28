<?php

namespace Municipio\Helper;

/**
 *
 * The TranslatedLabels class provides a method to retrieve translated labels.
 */
class TranslatedLabels
{
    /**
     * Get translated labels.
     *
     * @param array $labels Custom labels.
     *
     * @return object
     */
    public static function getLang($labels = [])
    {
        $lang = array(
            'goToHomepage'           => __("Go to homepage", 'municipio'),
            'jumpToMainMenu'         => __("Jump to the main menu", 'municipio'),
            'skipToMainContent'      => __("Skip to the main content", 'municipio'),
            'jumpToMainContent'      => __("Jump to the main content", 'municipio'),
            'skipToMainMenu'         => __("Skip to the main menu", 'municipio'),
            'skipToSideMenu'         => __("Skip to the side menu", 'municipio'),
            'ago'                    => __("ago", 'municipio'),
            'now'                    => __("just now", 'municipio'),
            'since'                  => __("since", 'municipio'),
            'years'                  => __("years", 'municipio'),
            'year'                   => __("year", 'municipio'),
            'months'                 => __("months", 'municipio'),
            'month'                  => __("month", 'municipio'),
            'weeks'                  => __("weeks", 'municipio'),
            'week'                   => __("week", 'municipio'),
            'days'                   => __("days", 'municipio'),
            'day'                    => __("day", 'municipio'),
            'hours'                  => __("hours", 'municipio'),
            'hour'                   => __("hour", 'municipio'),
            'minutes'                => __("minutes", 'municipio'),
            'minute'                 => __("minute", 'municipio'),
            'seconds'                => __("seconds", 'municipio'),
            'second'                 => __("second", 'municipio'),
            'search'                 => __("Search", 'municipio'),
            'searchOn'               => __("Search on", 'municipio'),
            'searchQuestion'         => __("What are you searching for?", 'municipio'),
            'searchResults'          => __("Searchresults", 'municipio'),
            'primaryNavigation'      => __("Primary navigation", 'municipio'),
            'megaNavigation'         => __("Mega menu", 'municipio'),
            'quicklinksNavigation'   => __("Useful links", 'municipio'),
            'relatedLinks'           => __("Related links", 'municipio'),
            'menu'                   => __("Menu", 'municipio'),
            'emblem'                 => __("Site emblem", 'municipio'),
            'close'                  => __("Close", 'municipio'),
            'moreLanguages'          => __("More Languages", 'municipio'),
            'changeLanguage'         => __("Language", 'municipio'),
            'expand'                 => __("Expand", 'municipio'),
            'breadcrumbPrefix'       => __("You are here: ", 'municipio'),
            'readingTime'            => __('Reading time', 'municipio'),
            'related'                => _x('Related', 'Related (name of posttype)', 'municipio'),
            'showAll'                => __('Show all', 'municipio'),
            'readMore'               => __('Read more', 'municipio'),
            'bookHere'               => __('Book here', 'municipio'),
            'updated'                => __('Updated', 'municipio'),
            'publish'                => __('Published', 'municipio'),
            'by'                     => __('Published by', 'municipio'),
            'on'                     => __('on', 'municipio'),
            'of'                     => __('of', 'municipio'),
            'filterBtn'              => __('Filter', 'municipio'),
            'resetFilterBtn'         => __('Reset filter', 'municipio'),
            'noResult'               => __('No items found.', 'municipio'),
            'sortBy'                 => __('Sort by', 'municipio'),
            'sortRandom'             => __('Random', 'municipio'),
            'sortName'               => __('Name (A-Z)', 'municipio'),
            'sortPublishDate'        => __('Publish Date', 'municipio'),
            'published'              => __('Published', 'municipio'),
            'updated'                => __('Updated', 'municipio'),
            'readMore'               => __('Read more', 'municipio'),
            'fromDate'               => __('Choose a from date', 'municipio'),
            'toDate'                 => __('Choose a to date', 'municipio'),
            'dateInvalid'            => __('Select a valid date', 'municipio'),
            'searchBtn'              => __('Search', 'municipio'),
            'filterBtn'              => __('Filter', 'municipio'),
            'resetSearchBtn'         => __('Reset search', 'municipio'),
            'resetFilterBtn'         => __('Reset filter', 'municipio'),
            'archiveNav'             => __('Archive navigation', 'municipio'),
            'resetFacetting'         => __('Reset', 'municipio'),
            'password'               => __('Password', 'municipio'),
            'usernameOrEmailAddress' => __('Username or Email Address', 'municipio'),

        );

        return (object) array_merge($labels, $lang);
    }
}
