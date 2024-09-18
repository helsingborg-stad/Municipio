<?php

namespace Municipio\Theme;

/**
 * Class Icon
 * @package Municipio\Theme
 */
class Icon
{
    private static $runtimeCache = [
        'altText' => null
    ];

    public function __construct()
    {
        add_filter(
            'ComponentLibrary/Component/Icon/AltTextPrefix',
            array($this, 'altTextPrefix'),
            10,
            1
        );

        add_filter(
            'ComponentLibrary/Component/Icon/AltText',
            array($this, 'altText'),
            10,
            1
        );

        add_filter(
            'ComponentLibrary/Component/Icon/altTextUndefined',
            array($this, 'altTextUndefined'),
            10,
            1
        );
    }

    public function altTextPrefix($altTextPrefix)
    {
        return __("Icon: ", 'municipio');
    }

    public function altText($altText)
    {
        if (!is_null(self::$runtimeCache['altText'])) {
            return self::$runtimeCache['altText'];
        }

        $altText = [
          'language'                    => __("Globe", 'municipio'),
          'menu'                        => __("Menu", 'municipio'),
          'date_range'                  => __("Calendar", 'municipio'),
          'search'                      => __("Magnifying glass", 'municipio'),
          'print'                       => __("Printer", 'municipio'),
          'thumb_up'                    => __("Thumb up", 'municipio'),
          'thumb_up_alt'                => __("Thumb up", 'municipio'),
          'thumb_down'                  => __("Thumb down", 'municipio'),
          'thumb_down_alt'              => __("Thumb down", 'municipio'),
          'thumb_up_off_alt'            => __('Thumb up', 'municipio'),
          'thumb_down_off_alt'          => __('Thumb down', 'municipio'),
          'email'                       => __("Letter", 'municipio'),
          'phone'                       => __("Phone", 'municipio'),
          'phonelink_ring'              => __('Phone', 'municipio'),
          'call'                        => __("Phone", 'municipio'),
          'facebook'                    => __("Facebook emblem", 'municipio'),
          'chat_bubble'                 => __("Chat bubble", 'municipio'),
          'chat_bubble_outline'         => __('Chat bubble', 'municipio'),
          'chat'                        => __('Chat bubble', 'municipio'),
          'close'                       => __("Close cross", 'municipio'),
          'clear'                       => __('Close cross', 'municipio'),
          'info'                        => __("Information", 'municipio'),
          'comment'                     => __("Comment", 'municipio'),
          'message'                     => __("Message", 'municipio'),
          'file_upload'                 => __('File upload', 'municipio'),
          'apps'                        => __('Apps', 'municipio'),
          'play_circle'                 => __('Play', 'municipio'),
          'play_arrow'                  => __('Play', 'municipio'),
          'play_circle_filled'          => __('Play', 'municipio'),
          'play_circle_outline'         => __('Play', 'municipio'),
          'volume_mute'                 => __('Volume muted', 'municipio'),
          'volume_up'                   => __('Volume up', 'municipio'),
          'volume_down'                 => __('Volume down', 'municipio'),
          'volume_off'                  => __('Volume off', 'municipio'),
          'people_outline'              => __('People', 'municipio'),
          'mail_outline'                => __('Mail', 'municipio'),
          'arrow_forward'               => __('Arrow forward', 'municipio'),
          'arrow_back'                  => __('Arrow back', 'municipio'),
          'chevron_right'               => __('Arrow forward', 'municipio'),
          'chevron_left'                => __('Arrow back', 'municipio'),
          'keyboard_arrow_right'        => __('Arrow right', 'municipio'),
          'keyboard_arrow_left'         => __('Arrow left', 'municipio'),
          'keyboard_arrow_down'         => __('Arrow down', 'municipio'),
          'keyboard_arrow_up'           => __('Arrow up', 'municipio'),
          'arrow_right'                 => __('Arrow right', 'municipio'),
          'arrow_left'                  => __('Arrow left', 'municipio'),
          'arrow_drop_up'               => __('Arrow up', 'municipio'),
          'arrow_drop_down'             => __('Arrow down', 'municipio'),
          'arrow_upward'                => __('Arrow up', 'municipio'),
          'arrow_circle_right'          => __('Arrow right', 'municipio'),
          'arrow_circle_left'           => __('Arrow left', 'municipio'),
          'arrow_circle_up'             => __('Arrow up', 'municipio'),
          'arrow_circle_down'           => __('Arrow down', 'municipio'),
          'get_app'                     => __('Download file', 'municipio'),
          'https'                       => __('Lock', 'municipio'),
          'launch'                      => __('Open', 'municipio'),
          'open_in_new'                 => __('Open', 'municipio'),
          'room'                        => __('Location point', 'municipio'),
          'location_on'                 => __('Location point', 'municipio'),
          'restore'                     => __('Restore', 'municipio'),
          'airplay'                     => __('Cast', 'municipio'),
          'perm_device_information'     => __('Device information', 'municipio'),
          'question_answer'             => __('Question answers', 'municipio'),
          'expand_more'                 => __('Expand section', 'municipio'),
          'expand_less'                 => __('Shrink section', 'municipio'),
          'report'                      => __('Warning', 'municipio'),
          'warning'                     => __('Warning', 'municipio'),
          'warning_amber'               => __('Warning', 'municipio'),
          'check_circle_outline'        => __('Checked', 'municipio'),
          'check_circle'                => __('Checked', 'municipio'),
          'error_outline'               => __('Error', 'municipio'),
          'error'                       => __('Error', 'municipio'),
          'check'                       => __('Check mark', 'municipio'),
          'add_circle'                  => __('Increase', 'municipio'),
          'add_circle_outline'          => __('Increase', 'municipio'),
          'add_box'                     => __('Increase', 'municipio'),
          'add'                         => __('Plus', 'municipio'),
          'remove_circle'               => __('Decrease', 'municipio'),
          'remove_circle_outline'       => __('Decrease', 'municipio'),
          'pause_circle'                => __('Pause', 'municipio'),
          'pause_circle_filled'         => __('Pause', 'municipio'),
          'pause'                       => __('Pause', 'municipio'),
          'arrow_outward'               => __('Open in external', 'municipio'),
          'calendar_today'              => __('Calendar', 'municipio'),
          'remove'                      => __('Minus', 'municipio'),
          'announcement'                => __('Announcement', 'municipio'),
          'insert_drive_file'           => __('File', 'municipio'),
          'attach_file'                 => __('Attachment', 'municipio'),
          'delete'                      => __('Bin', 'municipio'),
          'swap_vert'                   => __('Swap vertical direction', 'municipio'),
          'fullscreen'                  => __('Fullscreen', 'municipio'),
          'keyboard_double_arrow_right' => __('Arrows right', 'municipio'),
          'keyboard_double_arrow_left'  => __('Arrows left', 'municipio'),
          'access_time'                 => __('Clock', 'municipio'),
          'access_time_filled'          => __('Clock', 'municipio'),
          'money'                       => __('Money', 'municipio'),
          'calendar_month'              => __('Calendar', 'municipio'),
          'map'                         => __('Map', 'municipio'),
          'schedule'                    => __('Clock', 'municipio'),
          'content_copy'                => __('Copy', 'municipio'),
          'support_agent'               => __('Support', 'municipio'),
          'home'                        => __('House', 'municipio'),
        ];

        return self::$runtimeCache['altText'] = $altText;
    }

    public function altTextUndefined($altTextUndefined)
    {
        return __("Undefined", 'municipio');
    }
}
