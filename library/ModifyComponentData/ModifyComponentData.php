<?php

namespace Municipio\ModifyComponentData;

use WpService\WpService;

class ModifyComponentData
{
    public function __construct(
        private WpService $wpService,
    ) {}

    public function modifyFileInputLabels(): void
    {
        $this->wpService->addFilter(
            'ComponentLibrary/Component/Fileinput/Data',
            function (array $data): array {
                $data['allowedFileTypesLabel'] = $this->wpService->__('Allowed files types: ', 'municipio');
                $data['fileTypeVideosLabel'] = $this->wpService->__('Video', 'municipio');
                $data['fileTypeImagesLabel'] = $this->wpService->__('Image', 'municipio');
                $data['fileTypeAudioLabel'] = $this->wpService->__('Audio', 'municipio');
                $data['maximumSizeLabel'] = $this->wpService->__('Maximum size: ', 'municipio');
                $data['buttonLabel'] = $this->wpService->__('Select file', 'municipio');
                $data['buttonRemoveLabel'] = $this->wpService->__('Remove file', 'municipio');
                $data['buttonDropLabel'] = $this->wpService->__('Drop files here', 'municipio');

                return $data;
            },
        );
    }
}
