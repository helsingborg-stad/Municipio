<?php

$municipio_intranet_walkthrough_counter = 0;

if (!function_exists('municipio_intranet_walkthrough')) {
    /**
     * Creates a walkthrough step
     * @param  string $title             The step headline/title
     * @param  string $html              HTML content
     * @param  string $highlightSelector Selector for element to highlight when step is active
     * @param  string $position          The position of the blipper
     * @param  string $dropdownPosition  The position of dropdown
     * @param  array  $css               CSS rules ($key => $value)
     * @return string                    Walkthrough markup
     */
    function municipio_intranet_walkthrough($title, $html, $highlightSelector = null, $position = 'center', $dropdownPosition = 'center', $css = array())
    {
        if (!isset($_GET['walkthrough'])) {
            return;
        }

        global $municipio_intranet_walkthrough_counter;
        $municipio_intranet_walkthrough_counter++;

        if ($highlightSelector) {
            $highlightSelector = ' data-highlight="' . $highlightSelector . '"';
        }

        $styleTag = null;
        if (is_array($css) && count($css) > 0) {
            $styleTag = ' style="';
            foreach ($css as $key => $value) {
                $styleTag .= $key . ':' . $value . ';';
            }
            $styleTag .= '"';
        }

        $position = 'walkthrough-' . $position;

        switch ($dropdownPosition) {
            default:
                $dropdownPosition = 'walkthrough-dropdown-center';
                break;

            case 'left':
                $dropdownPosition = 'walkthrough-dropdown-left';
                break;

            case 'right':
                $dropdownPosition = 'walkthrough-dropdown-right';
                break;
        }

        return '
            <div class="walkthrough ' . $position . ' ' . $dropdownPosition . '"' . $highlightSelector . $styleTag . '>
                <div class="blipper" data-dropdown=".blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown"></div>
                <div class="dropdown-menu dropdown-menu-arrow blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown gutter">
                    <h4>' . $title . '</h4>
                    <p>
                        ' . $html . '
                    </p>
                    <footer>
                        <button class="btn" data-action="walkthrough-previous">' . __('Previous', 'municipio-intranet') . '</button>
                        <button class="btn" data-action="walkthrough-next">' . __('Next', 'municipio-intranet') . '</button>
                        <button class="btn btn-plain" data-action="walkthrough-cancel">' . __('Cancel', 'municipio-intranet') . '</button>
                    </footer>
                </div>
            </div>
        ';
    }
}

if (!function_exists('municipio_intranet_field_example')) {
    function municipio_intranet_field_example($key, $example, $label = null)
    {
        if (is_null($label)) {
            $label = __('Example', 'municipio-intranet');
        }

        $example = apply_filters('MunicipioIntranet/EditProfile/Example/Example', array(
            'label' => $label,
            'example' => $example
        ), $key);

        echo '<small class="form-example"><span>' . $example['label'] . ':</span> ' . $example['example'] . '</small>';
    }
}

if (!function_exists('municipio_current_post_status')) {
    function municipio_current_post_status()
    {
        global $wp_query;

        if (isset($wp_query->queried_object->post_status)) {
            return $wp_query->queried_object->post_status;
        }

        return false;
    }
}

if (!function_exists('municipio_intranet_follow_button')) {
    function municipio_intranet_follow_button($blogId, $additionalClasses = array(), $echo = true)
    {
        $additionalClasses = implode(' ', $additionalClasses);

        $html = '<button class="btn btn-primary btn-subscribe ' . $additionalClasses . '" data-subscribe="' . $blogId . '">';

        if (!\Intranet\User\Subscription::hasSubscribed($blogId)) {
            $html .= '<i class="pricon pricon-plus-o"></i> ' . __('Follow', 'municipio-intranet');
        } else {
            $html .= '<i class="pricon pricon-minus-o"></i> ' . __('Unfollow', 'municipio-intranet');
        }

        $html .= '</button>';

        if (!$echo) {
            return $html;
        }

        echo $html;
    }
}

if (!function_exists('municipio_intranet_get_user_search_stopwords')) {
    function municipio_intranet_get_user_search_stopwords()
    {
        return apply_filters(
            'MunicipioIntranet/user/search/stopwords',
            array(
                "aderton", "adertonde", "adjö", "aldrig", "alla", "allas", "allt", "alltid", "alltså", "än", "andra", "andras",
                "annan", "annat", "ännu", "artonde", "artonn", "åtminstone", "att", "åtta", "åttio", "åttionde", "åttonde", "av",
                "även", "båda", "bådas", "bakom", "bara", "bäst", "bättre", "behöva", "behövas", "behövde", "behövt", "beslut",
                "beslutat", "beslutit", "bland", "blev", "bli", "blir", "blivit", "bort", "borta", "bra", "då", "dag", "dagar",
                "dagarna", "dagen", "där", "därför", "de", "del", "delen", "dem", "den", "deras", "dess", "det", "detta", "dig",
                "din", "dina", "dit", "ditt", "dock", "du", "efter", "eftersom", "elfte", "eller", "elva", "en", "enkel",
                "enkelt", "enkla", "enligt", "er", "era", "ert", "ett", "ettusen", "få", "fanns", "får", "fått", "fem", "femte",
                "femtio", "femtionde", "femton", "femtonde", "fick", "fin", "finnas", "finns", "fjärde", "fjorton", "fjortonde",
                "fler", "flera", "flesta", "följande", "för", "före", "förlåt", "förra", "första", "fram", "framför", "från",
                "fyra", "fyrtio", "fyrtionde", "gå", "gälla", "gäller", "gällt", "går", "gärna", "gått", "genast", "genom",
                "gick", "gjorde", "gjort", "god", "goda", "godare", "godast", "gör", "göra", "gott", "ha", "hade", "haft", "han",
                "hans", "har", "här", "heller", "hellre", "helst", "helt", "henne", "hennes", "hit", "hög", "höger", "högre",
                "högst", "hon", "honom", "hundra", "hundraen", "hundraett", "hur", "i", "ibland", "idag", "igår", "igen",
                "imorgon", "in", "inför", "inga", "ingen", "ingenting", "inget", "innan", "inne", "inom", "inte", "inuti", "ja",
                "jag", "jämfört", "kan", "kanske", "knappast", "kom", "komma", "kommer", "kommit", "kr", "kunde", "kunna",
                "kunnat", "kvar", "länge", "längre", "långsam", "långsammare", "långsammast", "långsamt", "längst", "långt",
                "lätt", "lättare", "lättast", "legat", "ligga", "ligger", "lika", "likställd", "likställda", "lilla", "lite",
                "liten", "litet", "man", "många", "måste", "med", "mellan", "men", "mer", "mera", "mest", "mig", "min", "mina",
                "mindre", "minst", "mitt", "mittemot", "möjlig", "möjligen", "möjligt", "möjligtvis", "mot", "mycket", "någon",
                "någonting", "något", "några", "när", "nästa", "ned", "nederst", "nedersta", "nedre", "nej", "ner", "ni", "nio",
                "nionde", "nittio", "nittionde", "nitton", "nittonde", "nödvändig", "nödvändiga", "nödvändigt", "nödvändigtvis",
                "nog", "noll", "nr", "nu", "nummer", "och", "också", "ofta", "oftast", "olika", "olikt", "om", "oss", "över",
                "övermorgon", "överst", "övre", "på", "rakt", "rätt", "redan", "så", "sade", "säga", "säger", "sagt", "samma",
                "sämre", "sämst", "sedan", "senare", "senast", "sent", "sex", "sextio", "sextionde", "sexton", "sextonde", "sig",
                "sin", "sina", "sist", "sista", "siste", "sitt", "sjätte", "sju", "sjunde", "sjuttio", "sjuttionde", "sjutton",
                "sjuttonde", "ska", "skall", "skulle", "slutligen", "små", "smått", "snart", "som", "stor", "stora", "större",
                "störst", "stort", "tack", "tidig", "tidigare", "tidigast", "tidigt", "till", "tills", "tillsammans", "tio",
                "tionde", "tjugo", "tjugoen", "tjugoett", "tjugonde", "tjugotre", "tjugotvå", "tjungo", "tolfte", "tolv", "tre",
                "tredje", "trettio", "trettionde", "tretton", "trettonde", "två", "tvåhundra", "under", "upp", "ur", "ursäkt", "ut",
                "utan", "utanför", "ute", "vad", "vänster", "vänstra", "var", "vår", "vara", "våra", "varför", "varifrån", "varit",
                "varken", "värre", "varsågod", "vart", "vårt", "vem", "vems", "verkligen", "vi", "vid", "vidare", "viktig",
                "viktigare", "viktigast", "viktigt", "vilka", "vilken", "vilket", "vill"
            )
            get_locale()
        );
    }
}
