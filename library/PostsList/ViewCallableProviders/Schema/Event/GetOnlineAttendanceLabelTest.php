<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use Municipio\PostObject\NullPostObject;
use Municipio\Schema\BaseType;
use Municipio\Schema\EventAttendanceModeEnumeration;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class GetOnlineAttendanceLabelTest extends TestCase
{
    #[TestDox('returns label when the event takes place online')]
    public function testReturnsLabelForOnlineEvent(): void
    {
        $result = $this->getLabel(EventAttendanceModeEnumeration::OnlineEventAttendanceMode);

        $this->assertSame('Takes place online', $result);
    }

    #[TestDox('returns null when the event is not online')]
    #[DataProvider('nonOnlineAttendanceModeProvider')]
    public function testReturnsNullWhenEventIsNotOnline(?string $attendanceMode): void
    {
        $this->assertNull($this->getLabel($attendanceMode));
    }

    public static function nonOnlineAttendanceModeProvider(): array
    {
        return [
            'offline' => [EventAttendanceModeEnumeration::OfflineEventAttendanceMode],
            'mixed'   => [EventAttendanceModeEnumeration::MixedEventAttendanceMode],
            'missing' => [null],
        ];
    }

    private function getLabel(?string $attendanceMode): ?string
    {
        $post = new class (Schema::event()->eventAttendanceMode($attendanceMode)) extends NullPostObject {
            public function __construct(private BaseType $schema)
            {
            }

            public function getSchema(): BaseType
            {
                return $this->schema;
            }
        };

        $wpService = new FakeWpService(['__' => fn(string $text) => $text]);

        return (new GetOnlineAttendanceLabel($wpService))->getCallable()($post);
    }
}
