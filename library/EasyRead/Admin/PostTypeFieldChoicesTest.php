<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Admin;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostTypes;

class PostTypeFieldChoicesTest extends TestCase
{
    #[TestDox('offers registered public post types as searchable selection choices')]
    public function testAddsPublicPostTypesAsChoices(): void
    {
        $wpService = new class implements AddFilter, GetPostTypes {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function getPostTypes(array|string $args = [], string $output = 'names', string $operator = 'and'): array
            {
                return [
                    (object) ['name' => 'page', 'label' => 'Pages'],
                    (object) ['name' => 'post', 'label' => 'Posts'],
                ];
            }
        };

        $field = (new PostTypeFieldChoices($wpService))->addChoices(['choices' => []]);

        static::assertSame(['page' => 'Pages', 'post' => 'Posts'], $field['choices']);
    }
}
