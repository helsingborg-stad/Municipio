<?php

namespace Modularity\Module\Audio;

class Audio extends \Modularity\Module {
    public $slug = 'audio';
    public $supports = array();
    public $isBlockCompatible = true;

    public function init()
    {
        $this->nameSingular = __("Audio", 'modularity');
        $this->namePlural = __("Audio", 'modularity');
        $this->description = __("Outputs an audio module.", 'modularity');
    }

    public function data(): array
    {
        $data = array();

        $fields = $this->getFields();
        
        $data['ID'] = $this->ID;
        $data['requiresAcceptance'] = $this->checkIfRequiresAcceptance($fields);
        $data['fileType'] = $fields['mod_audio_filetype'] ?? null;
        $data['url'] = $fields['mod_audio_filetype'] === 'local' ? $fields['mod_audio_local_file'] : $fields['mod_audio_external_audio_url'];
        $data['alignment'] = $fields['mod_audio_alignment'] ?? 'start';
        $data['acceptanceLabels'] = $data['requiresAcceptance'] ? \Modularity\Helper\AcceptanceLabels::getLabels() : null;

        return $data;
    }

    private function checkIfRequiresAcceptance($fields): bool
    {
        if ($fields['mod_audio_filetype'] === 'local' || empty($fields['mod_audio_external_audio_url'])) {
            return false;
        }
        
        return true;
    }

}