<?php

namespace Modularity\Module\Script;

class Script extends \Modularity\Module
{
    public $slug = 'script';
    public $supports = array();
    public $isBlockCompatible = true;

    public function init()
    {
        $this->nameSingular = __("Script", 'modularity');
        $this->namePlural = __("Script", 'modularity');
        $this->description = __("Outputs unsanitized code to widget area.", 'modularity');

        //Remove html filter
        add_action('save_post', array($this, 'disableHTMLFiltering'), 5);

        add_filter('acf/validate_value/name=embed_code', array($this, 'validateEmbedCode'), 10, 4);
    }

    public function validateEmbedCode($valid, $value, $field, $input_name) {
        $pattern = '/<iframe|<video/';

        if (preg_match($pattern, $value)) {
            return __("Please use a more appropriate module for your content. (video or iframe module)", 'modularity');
        }
        
        return $valid;
    }

    public function data() : array
    {
        $data = array();

        /* Parsing the embed code and extracting the scripts, iframes, links and styles. */
        $embed = get_field('embed_code', $this->ID);

        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $embed, LIBXML_NOERROR);

        $xpath = new \DOMXpath($doc);
        $allowedElements = $xpath->query('//script | //iframe | //link | //style');

        $data['embedContent'] =
        is_admin() ?
        '<pre>' . htmlspecialchars($embed) . '</pre>' :
        $embed;
        
        for ($i = 0; $i < $allowedElements->length; $i++) {
            $element = $allowedElements->item($i);

            $data['embed'][$i]['src'] = null;
            $data['embed'][$i]['requiresAccept'] = 1;

            switch ($element->tagName) {
                case 'script':

                    $data['embed'][$i]['src'] = null;
                    $data['embed'][$i]['requiresAccept'] = 0;

                    $doc->saveHTML($element->setAttribute('defer', true));

                    $src = $element->getAttribute('src');
                    if (!empty($src)) {
                        $data['embed'][$i]['requiresAccept'] = 1;
                        $data['embed'][$i]['src'] = $src;
                    }
                    break;

                case 'iframe':
                    $src = $element->getAttribute('src');

                    $data['embed'][$i]['requiresAccept'] = 1;
                    $data['embed'][$i]['src'] = $src;

                    if (empty($src)) {
                        $data['embed'][$i]['requiresAccept'] = 0;
                        $data['embed'][$i]['src'] = null;
                    }
                    break;
                case 'link':
                    $href = $element->getAttribute('href');
                    
                    $data['embed'][$i]['requiresAccept'] = 1;
                    $data['embed'][$i]['src'] = $href;

                    if (empty($href)) {
                        $data['embed'][$i]['requiresAccept'] = 0;
                        $data['embed'][$i]['src'] = null;
                    }
                    break;
                case 'style':
                    $data['embed'][$i]['requiresAccept'] = 0;
                    $data['embed'][$i]['src'] = null;
                    break;

                default:
                    // no action necessary
                    break;
            }
        }

        $data['scriptWrapWithClassName'] = get_field('script_wrap_with', $this->ID) ?? 'card';

        $placeholder = get_field('embedded_placeholder_image', $this->ID);
        $attachment = !empty($placeholder) ? 
        wp_get_attachment_image_src($placeholder['ID'], [1000, false]) : false;
        if (!empty($attachment)) {
            $data['placeholder'] = [
                'url' => $attachment[0],
                'width' => $attachment[1],
                'height' => $attachment[2],
                'alt' => $placeholder['alt']
            ];
        }

        $embededCardPadding = get_field('embeded_card_padding', $this->ID);
        $data['scriptPadding'] =
        (bool) $embededCardPadding ?
        "u-padding__y--{$embededCardPadding} u-padding__x--$embededCardPadding" :
        '';

        $data['lang'] = \Modularity\Helper\AcceptanceLabels::getLabels();
        $requiresAccept = false;
        $arrSrc = array();
        
        foreach ($data['embed'] ?? [] as $embedSrc) {
            if ($embedSrc['requiresAccept'] == 1) {
                $requiresAccept = true;
            }
            if($embedSrc['src']) {
                array_push(
                    $arrSrc, 
                    $this->normalizeUrl($embedSrc['src'])
                );
            }
        }
        $data['scriptSrcArray'] = $arrSrc;
        $data['requiresAccept'] = $requiresAccept; 

        return $data;
    }

    /**
     * Adds scheme to url if not defined in url.
     */
    private function normalizeUrl($url) {
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;  
        }
        return $url; 
    }

    /**
     * Removes the filter of html & script data before save.
     * @var int
     */
    public function disableHTMLFiltering($postId)
    {
        //Bail early if not a script module save
        if (get_post_type($postId) !== "mod-" . $this->slug) {
            return;
        }

        //Disable filter temporarirly
        add_filter('acf/allow_unfiltered_html', function ($allow_unfiltered_html) {
            return true;
        });
    }


    public function template()
    {
        return $this->data['scriptWrapWithClassName'] . '.blade.php';
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
