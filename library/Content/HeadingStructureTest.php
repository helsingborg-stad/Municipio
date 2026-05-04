<?php

namespace Municipio\Content;

use Municipio\Content\HeadingStructure\Contracts\HeadingStructureDomAdapterInterface;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class HeadingStructureTest extends TestCase
{
    public function testAddHooksRegistersHtmlOutputFilter(): void
    {
        $wpService = new class implements AddFilter {
            public array $filtersAdded = [];

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->filtersAdded[] = [$hookName, $callback, $priority, $acceptedArgs];

                return true;
            }
        };

        $subject = new HeadingStructure($wpService, new FakeHeadingStructureDomAdapter([]));
        $subject->addHooks();

        $this->assertSame('Website/HTML/output', $wpService->filtersAdded[0][0]);
    }

    public function testCorrectHeadingStructurePromotesAutoPromoteCandidateWhenNoH1Exists(): void
    {
        $subject = new HeadingStructure(
            $this->createStub(AddFilter::class),
            new FakeHeadingStructureDomAdapter([
                new FakeHeadingStructureElement('div', true),
                new FakeHeadingStructureElement('h3'),
            ]),
        );

        $result = $subject->correctHeadingStructure('<div data-autopromote="1">Title</div><h3>Subtitle</h3>');

        $this->assertSame('h1,h2', $result);
    }

    public function testCorrectHeadingStructurePreventsDuplicateH1AndSkippedLevels(): void
    {
        $subject = new HeadingStructure(
            $this->createStub(AddFilter::class),
            new FakeHeadingStructureDomAdapter([
                new FakeHeadingStructureElement('h1'),
                new FakeHeadingStructureElement('h1'),
                new FakeHeadingStructureElement('h4'),
            ]),
        );

        $result = $subject->correctHeadingStructure('<h1>Title</h1><h1>Another</h1><h4>Deep</h4>');

        $this->assertSame('h1,h2,h3', $result);
    }

    public function testCorrectHeadingStructureReturnsOriginalHtmlWhenAdapterFails(): void
    {
        $subject = new HeadingStructure(
            $this->createStub(AddFilter::class),
            new class implements HeadingStructureDomAdapterInterface {
                public function load(string $html): void
                {
                    throw new \RuntimeException('Unable to load HTML.');
                }

                public function getHeadingElements(): array
                {
                    return [];
                }

                public function findAutoPromoteCandidate(): ?object
                {
                    return null;
                }

                public function getTagName(object $element): string
                {
                    return '';
                }

                public function renameHeadingElement(object $element, string $newTag): void
                {
                }

                public function saveHtml(): string
                {
                    return '';
                }
            },
        );

        $html = '<h2>Title</h2>';

        $this->assertSame($html, $subject->correctHeadingStructure($html));
    }
}

class FakeHeadingStructureDomAdapter implements HeadingStructureDomAdapterInterface
{
    /**
     * @param array<FakeHeadingStructureElement> $elements
     */
    public function __construct(private array $elements)
    {
    }

    public function load(string $html): void
    {
    }

    public function getHeadingElements(): array
    {
        return array_values(array_filter(
            $this->elements,
            fn (FakeHeadingStructureElement $element): bool => (bool) preg_match('/^h[1-6]$/', $element->tagName),
        ));
    }

    public function findAutoPromoteCandidate(): ?object
    {
        foreach ($this->elements as $element) {
            if ($element->autoPromote) {
                return $element;
            }
        }

        return null;
    }

    public function getTagName(object $element): string
    {
        return strtolower($element->tagName);
    }

    public function renameHeadingElement(object $element, string $newTag): void
    {
        $element->tagName = $newTag;
    }

    public function saveHtml(): string
    {
        return implode(',', array_map(
            fn (FakeHeadingStructureElement $element): string => $element->tagName,
            $this->getHeadingElements(),
        ));
    }
}

class FakeHeadingStructureElement
{
    public function __construct(public string $tagName, public bool $autoPromote = false)
    {
    }
}
