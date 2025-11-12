<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;

class GetDateFilterFieldArgumentsTest extends TestCase
{
    #[TestDox('It should return correct date filter field arguments')]
    public function testGetDateFilterFieldArguments(): void
    {
        $getPostsConfig = new class extends DefaultGetPostsConfig{
            public function getDateFrom(): ?string
            {
                return '2025-11-07';
            }

            public function getDateTo(): ?string
            {
                return '2025-11-09';
            }
        };

        $getDateFilterFieldArguments = new GetDateFilterFieldArguments($getPostsConfig, $this->createWpService(), 'from', 'to');
        $callable                    = $getDateFilterFieldArguments->getCallable();
        $result                      = $callable();

        $this->assertEquals([
            'from' => [
                'type'          => 'date',
                'name'          => 'from',
                'value'         => '2025-11-07',
                'label'         => 'Choose a from date',
                'attributeList' => [
                    'data-invalid-message'   => 'Select a valid date',
                    'js-archive-filter-from' => ''
                ],
                'required'      => false,
                'datepicker'    => [
                    'title'              => 'Choose a from date',
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
                'value'         => '2025-11-09',
                'label'         => 'Choose a to date',
                'attributeList' => [
                    'data-invalid-message' => 'Select a valid date',
                    'js-archive-filter-to' => ''
                ],
                'required'      => false,
                'datepicker'    => [
                    'title'              => 'Choose a to date',
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
        ], $result);
    }

    private function createWpService(): __
    {
        return new class implements __ {
            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };
    }
}
