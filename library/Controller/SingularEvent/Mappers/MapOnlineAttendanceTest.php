<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Action;
use Municipio\Schema\EventAttendanceModeEnumeration;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MapOnlineAttendanceTest extends TestCase
{
    private const URL = 'https://teams.microsoft.com/l/meetup-join/abc';

    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(MapOnlineAttendance::class, new MapOnlineAttendance($this->getWpService()));
    }

    #[TestDox('returns notice and button for an online event with a JoinAction')]
    public function testOnlineEventWithJoinAction(): void
    {
        $event = $this->getOnlineEvent()->potentialAction([$this->getJoinAction()]);

        $this->assertSame([
            'notice' => 'The event has no physical location and takes place online.',
            'button' => [
                'text'      => 'Delta i onlineevent',
                'href'      => self::URL,
                'ariaLabel' => 'Delta i onlineevent, The link opens in a new window',
            ],
        ], $this->map($event));
    }

    #[TestDox('returns notice but no button for an online event without a JoinAction')]
    public function testOnlineEventWithoutJoinAction(): void
    {
        $result = $this->map($this->getOnlineEvent());

        $this->assertSame('The event has no physical location and takes place online.', $result['notice']);
        $this->assertNull($result['button']);
    }

    #[TestDox('returns null when the event is not online')]
    #[DataProvider('nonOnlineAttendanceModeProvider')]
    public function testReturnsNullWhenEventIsNotOnline(?string $attendanceMode): void
    {
        $event = Schema::event()
            ->eventAttendanceMode($attendanceMode)
            ->potentialAction([$this->getJoinAction()]);

        $this->assertNull($this->map($event));
    }

    public static function nonOnlineAttendanceModeProvider(): array
    {
        return [
            'offline' => [EventAttendanceModeEnumeration::OfflineEventAttendanceMode],
            'mixed'   => [EventAttendanceModeEnumeration::MixedEventAttendanceMode],
            'missing' => [null],
        ];
    }

    #[TestDox('accepts potentialAction as a single object')]
    public function testSinglePotentialAction(): void
    {
        $event = $this->getOnlineEvent()->potentialAction($this->getJoinAction());

        $this->assertSame(self::URL, $this->map($event)['button']['href']);
    }

    #[TestDox('accepts a JoinAction object')]
    public function testJoinActionObject(): void
    {
        $event = $this->getOnlineEvent()->potentialAction([
            Schema::joinAction()->description('Anslut')->url(self::URL),
        ]);

        $this->assertSame('Anslut', $this->map($event)['button']['text']);
    }

    #[TestDox('ignores actions that are not JoinActions')]
    public function testIgnoresOtherActions(): void
    {
        $event = $this->getOnlineEvent()->potentialAction([
            Schema::action()->setProperty('type', 'BuyAction')->url('https://example.com/ticket'),
            $this->getJoinAction(),
        ]);

        $this->assertSame(self::URL, $this->map($event)['button']['href']);
    }

    #[TestDox('ignores potentialAction entries that are not actions')]
    public function testIgnoresNonActionEntries(): void
    {
        $event = $this->getOnlineEvent()->potentialAction(['not an action', Schema::thing(), $this->getJoinAction()]);

        $this->assertSame(self::URL, $this->map($event)['button']['href']);
    }

    #[TestDox('uses default button text when description is empty')]
    #[DataProvider('emptyDescriptionProvider')]
    public function testDefaultButtonText(?string $description): void
    {
        $action = Schema::action()->setProperty('type', 'JoinAction')->url(self::URL);
        $action->setProperty('description', $description);

        $result = $this->map($this->getOnlineEvent()->potentialAction([$action]));

        $this->assertSame('Join the online event', $result['button']['text']);
        $this->assertSame('Join the online event, The link opens in a new window', $result['button']['ariaLabel']);
    }

    public static function emptyDescriptionProvider(): array
    {
        return [
            'missing'    => [null],
            'whitespace' => ['   '],
        ];
    }

    #[TestDox('does not return a button when the url is invalid or not http(s)')]
    #[DataProvider('invalidUrlProvider')]
    public function testInvalidUrl(string $url): void
    {
        $event = $this->getOnlineEvent()->potentialAction([$this->getJoinAction($url)]);

        $this->assertNull($this->map($event)['button']);
    }

    public static function invalidUrlProvider(): array
    {
        return [
            'not a url'          => ['not-a-valid-url'],
            'javascript'         => ['javascript:alert(1)'],
            'javascript slashes' => ['javascript://comment%0Aalert(1)'],
            'data'               => ['data://text/html,<script>alert(1)</script>'],
            'ftp'                => ['ftp://example.com/file'],
        ];
    }

    #[TestDox('restricts urls to http and https via escUrlRaw')]
    public function testUsesEscUrlRawWithHttpProtocols(): void
    {
        $wpService = $this->getWpService();
        $mapper    = new MapOnlineAttendance($wpService);

        $mapper->map($this->getOnlineEvent()->potentialAction([$this->getJoinAction()]));

        $this->assertSame([self::URL, ['http', 'https']], $wpService->methodCalls['escUrlRaw'][0]);
    }

    private function map(\Municipio\Schema\Event $event): ?array
    {
        return (new MapOnlineAttendance($this->getWpService()))->map($event);
    }

    private function getOnlineEvent(): \Municipio\Schema\Event
    {
        return Schema::event()->eventAttendanceMode(EventAttendanceModeEnumeration::OnlineEventAttendanceMode);
    }

    /**
     * Matches the format from the events API: "@type": "Action", "type": "JoinAction".
     */
    private function getJoinAction(string $url = self::URL): Action
    {
        return Schema::action()
            ->setProperty('type', 'JoinAction')
            ->description('Delta i onlineevent')
            ->url($url);
    }

    /**
     * escUrlRaw mimics WordPress by returning an empty string for disallowed protocols.
     */
    private function getWpService(): FakeWpService
    {
        return new FakeWpService([
            '__'        => fn(string $text) => $text,
            'escUrlRaw' => fn(string $url, ?array $protocols = null) =>
                in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), $protocols ?? [], true) ? $url : '',
        ]);
    }
}
