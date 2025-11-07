<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\__;

class GetDateFilterFieldArguments implements ViewCallableProviderInterface
{
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private __ $wpService
    ) {
    }

    public function getCallable(): callable
    {
        return function () {
            $fromValue = $this->getPostsConfig->getDateFrom();
            $toValue   = $this->getPostsConfig->getDateTo();

            return [
                'from' => [
                    'type'          => 'date',
                    'name'          => 'from',
                    'value'         => $fromValue,
                    'label'         => $this->wpService->__('Choose a from date', 'municipio'),
                    'attributeList' => [
                        'data-invalid-message'   => $this->wpService->__('Select a valid date', 'municipio'),
                        'js-archive-filter-from' => ''
                    ],
                    'required'      => false,
                    'datepicker'    => [
                        'title'              => $this->wpService->__('Choose a from date', 'municipio'),
                        'minDate'            => false,
                        'maxDate'            => false,
                        'required'           => true,
                        'showResetButton'    => true,
                        'showDaysOutOfMonth' => true,
                        'showClearButton'    => true,
                        'hideOnBlur'         => true,
                        'hideOnSelect'       => false
                    ]
                ],
                'to'   => [
                    'type'          => 'date',
                    'name'          => 'to',
                    'value'         => $toValue,
                    'label'         => $this->wpService->__('Choose a to date', 'municipio'),
                    'attributeList' => [
                        'data-invalid-message' => $this->wpService->__('Select a valid date', 'municipio'),
                        'js-archive-filter-to' => ''
                    ],
                    'required'      => false,
                    'datepicker'    => [
                        'title'              => $this->wpService->__('Choose a to date', 'municipio'),
                        'minDate'            => false,
                        'maxDate'            => false,
                        'required'           => true,
                        'showResetButton'    => true,
                        'showDaysOutOfMonth' => true,
                        'showClearButton'    => true,
                        'hideOnBlur'         => true,
                        'hideOnSelect'       => false
                    ]
                ]
            ];
        };
    }
}
