<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\HomeUrl;

class CurrentUrlTest extends TestCase
{
    #[TestDox('does not duplicate a WordPress subdirectory in the current URL')]
    public function testDoesNotDuplicateHomePath(): void
    {
        $_SERVER['REQUEST_URI'] = '/helsingborg/nato/';

        $wpService = $this->createMock(HomeUrl::class);
        $wpService->expects(static::exactly(2))
            ->method('homeUrl')
            ->willReturnMap([
                ['', null, 'http://localhost:8080/helsingborg'],
                ['nato/', null, 'http://localhost:8080/helsingborg/nato/'],
            ]);

        static::assertSame(
            'http://localhost:8080/helsingborg/nato/',
            (new CurrentUrl($wpService))->get(),
        );
    }
}
