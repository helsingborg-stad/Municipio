<?php

namespace Municipio\GlobalNotices;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class GetAndApplyGlobalNoticesTest extends TestCase
{
    public function testMapGlobalNoticeIncludesTitleWhenProvided(): void
    {
        $sut = new GetAndApplyGlobalNotices(
            new FakeWpService(),
            new FakeAcfService(),
            new GlobalNoticesConfig()
        );

        $result = $sut->mapGlobalNotice([
            'title'    => 'Important update',
            'message'  => 'The library closes early today.',
            'location' => 'banner',
            'type'     => 'info',
        ]);

        $this->assertSame('Important update', $result['message']['title']);
        $this->assertSame('The library closes early today.', $result['message']['text']);
    }

    public function testMapGlobalNoticeSetsNullTitleWhenMissing(): void
    {
        $sut = new GetAndApplyGlobalNotices(
            new FakeWpService(),
            new FakeAcfService(),
            new GlobalNoticesConfig()
        );

        $result = $sut->mapGlobalNotice([
            'message'  => 'The library closes early today.',
            'location' => 'banner',
            'type'     => 'info',
        ]);

        $this->assertNull($result['message']['title']);
        $this->assertSame('The library closes early today.', $result['message']['text']);
    }
}
