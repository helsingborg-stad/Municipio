<?php

namespace Modularity\Module\FilesList;

use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;

class FilesList extends \Modularity\Module
{
    public $slug = 'fileslist';
    public $supports = [];

    public function init()
    {
        $this->nameSingular = __("Files", 'modularity');
        $this->namePlural = __("Files", 'modularity');
        $this->description = __("Outputs a file archive.", 'modularity');
    }

    /**
     * Magic function for collecting template data.
     *
     * @return array Data for template.
     */
    public function data(): array
    {
        $fields = $this->getFields();
        $settings = isset($fields['settings']) && is_array($fields['settings']) ? $fields['settings'] : [];

        $data = [];
        $data['rows'] = $this->prepareFileData();
        $data['classes'] = implode(
            ' ',
            apply_filters(
                'Modularity/Module/Classes',
                array('c-card--default'),
                $this->post_type,
                $this->args
            )
        );
        $data['isFilterable'] = $fields['show_filter'] ?? false;
        $data['filterAboveCard'] = $fields['filter_above_card'] ?? false;
        $data['showDownloadIcon'] = in_array('show_download_icon', $settings);
        $data['uID'] = uniqid();
        $data['ID'] = $this->ID;

        return $data;
    }

    /**
     * Prepare array of file data into rows of the list.
     *
     * @return array All file data.
     */
    public function prepareFileData()
    {
        $fields = $this->getFields();
        $files = $fields['file_list'] ?? [];
        $settings = $fields['settings'] ?? [];
        $rows = [];

        foreach ($files as $key => $item) {
            $meta = [];
            $rows[$key] = [
                'title' => $this->filenameToTitle($item['file']['title'] ?? ''),
                'href' => $item['file']['url'] ?? '',
                'description' => $item['file']['description'] ?? '',
                'icon' => $this->getIconClass($item['file']['subtype'])
            ];

            if (!is_array($settings) || !in_array('hide_filetype', $settings)) {
                $meta[] = pathInfo($item['file']['url'], PATHINFO_EXTENSION);
            }

            if (!is_array($settings) || !in_array('hide_filesize', $settings)) {
                $meta[] = $this->formatBytes($item['file']['filesize']);
            }

            $rows[$key]['meta'] = $meta;
        }

        return $rows;
    }

    /**
     * Make filename more readable, when alternative not found.
     *
     * @param string $filename
     * @return string
     */
    private function filenameToTitle(string $filename): string
    {
        $wpService = WpService::get();

        if ($filename == $wpService->sanitizeTitle($filename)) {
            $filename = str_replace(['-', '_'], [' ', ' '], $filename);
        }

        return ucfirst($filename);
    }

    /**
     * Get icon class from type
     *
     * @return string
     */
    private function getIconClass($type): string
    {
        switch ($type) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
            case 'tif':
            case 'tiff':
            case 'webp':
            case 'avif':
            case 'svg':
            case 'heic':
            case 'heif':
            case 'raw':
            case 'dng':
                return 'photo_library';
            case 'mp4':
            case 'mov':
            case 'wmv':
            case 'avi':
            case 'webm':
                return 'video_file';
            case 'mp3':
            case 'aac':
            case 'wav':
            case 'aiff':
            case 'flac':
                return 'audio_file';
            case 'zip':
            case 'tar':
            case 'gz':
            case '7z':
            case 'tgz':
            case 'bz2':
            case 'rar':
                return 'folder_zip';
            case 'pdf':
                return 'picture_as_pdf';
        }

        return 'insert_drive_file';
    }

    /**
     * Format bytes as KB, MB etc. representation of the size.
     *
     * @return string Largest suffix possible.
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
    
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
