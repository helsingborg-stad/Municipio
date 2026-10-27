<?php

namespace Municipio\Controller\Sponsor;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Municipio\Schema\BaseType;
use Municipio\Schema\SponsorDemandEvent;
use Municipio\Schema\SponsorOffer;
use WpService\WpService;

/**
 * Maps native sponsor schemas to view data. Descriptions contain sanitized HTML;
 * other text is escaped by Blade. Empty strings and lists mean no content.
 *
 * @phpstan-type SponsorImage array{src: string, alt: string}
 * @phpstan-type SponsorContact array{
 *     name: string, role: string, email: string,
 *     telephone: string, telephoneHref: string
 * }
 * @phpstan-type SponsorRequirement array{
 *     name: string, description: string, counterPerformance: list<string>
 * }
 * @phpstan-type SponsorOrganization array{
 *     name: string, taxID: string, description: string, url: string,
 *     contacts: list<SponsorContact>, requirements: list<SponsorRequirement>
 * }
 * @phpstan-type SponsorRelatedOffer array{
 *     name: string, description: string, categories: list<string>,
 *     deadline: string, requirements: list<SponsorRequirement>
 * }
 * @phpstan-type SponsorItem array{name: string, description: string}
 */
class SponsorPresentation
{
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * @return array{
     *     description: string,
     *     images: list<SponsorImage>,
     *     startDate: string,
     *     endDate: string,
     *     locations: list<string>,
     *     categories: list<string>,
     *     activities: list<string>,
     *     deadline: string,
     *     organizations: list<SponsorOrganization>,
     *     contacts: list<SponsorContact>,
     *     requirements: list<SponsorRequirement>,
     *     offers: list<SponsorRelatedOffer>,
     *     items: list<SponsorItem>
     * }
     */
    public function map(SponsorOffer|SponsorDemandEvent $schema): array
    {
        if ($schema instanceof SponsorDemandEvent) {
            $organizations = $schema->getProperty('organisation') ?: $schema->getProperty('organizer');
        } else {
            $organizations = $schema->getProperty('offeredBy') ?: $schema->getProperty('seller');
        }

        return [
            'description' => $this->richText($schema->getProperty('description')),
            'images' => $this->mapImages($schema->getProperty('image')),
            'startDate' => $this->date($schema->getProperty('startDate')),
            'endDate' => $this->date($schema->getProperty('endDate')),
            'locations' => $this->mapLocations($schema->getProperty('location')),
            'categories' => $this->labels($schema->getProperty('category')),
            'activities' => $this->labels($schema->getProperty('keywords')),
            'deadline' => $this->date($schema->getProperty('availabilityEnds')),
            'organizations' => $this->mapOrganizations($organizations),
            'contacts' => $this->mapContacts($schema->getProperty('contactPoint')),
            'requirements' => $this->mapRequirements($schema->getProperty('demand')),
            'offers' => $this->mapOffers($schema->getProperty('hasSponsorshipOffer')),
            'items' => $this->mapItems($schema->getProperty('itemOffered')),
        ];
    }

    /** @return list<string> */
    private function mapLocations(mixed $value): array
    {
        $locations = [];
        foreach ($this->asList($value) as $place) {
            $location = $this->text($place);
            if ($location === '') {
                $parts = [
                    $this->text($this->property($place, 'name')),
                    $this->text($this->property($place, 'description')),
                    $this->mapAddress($this->property($place, 'address')),
                ];
                $parts = array_filter($parts, fn($part) => $part !== '');
                $location = implode(', ', array_unique($parts));
            }
            if ($location !== '') {
                $locations[] = $location;
            }
        }
        return $locations;
    }

    /** An address can be plain text or a PostalAddress schema object. */
    private function mapAddress(mixed $address): string
    {
        $text = $this->text($address);
        if ($text !== '') {
            return $text;
        }

        $parts = [];
        foreach (['streetAddress', 'postalCode', 'addressLocality', 'addressRegion', 'addressCountry'] as $property) {
            $part = $this->text($this->property($address, $property));
            if ($part !== '') {
                $parts[] = $part;
            }
        }
        return implode(', ', $parts);
    }

    /** @return list<SponsorOrganization> */
    private function mapOrganizations(mixed $organizations): array
    {
        $organizationDetails = [];
        foreach ($this->asList($organizations) as $organization) {
            $detail = [
                'name' => $this->text($organization) ?: $this->text($this->property($organization, 'name')),
                'taxID' => $this->text($this->property($organization, 'taxID')),
                'description' => $this->richText($this->property($organization, 'description')),
                'url' => $this->url($this->property($organization, 'url')),
                'contacts' => $this->mapContacts($this->property($organization, 'contactPoint')),
                'requirements' => $this->mapRequirements($this->property($organization, 'demand')),
            ];
            if (array_filter($detail)) {
                $organizationDetails[] = $detail;
            }
        }
        return $organizationDetails;
    }

    /** @return list<SponsorRelatedOffer> */
    private function mapOffers(mixed $value): array
    {
        $offers = [];
        foreach ($this->asList($value) as $offer) {
            $detail = [
                'name' => $this->text($this->property($offer, 'name')),
                'description' => $this->richText($this->property($offer, 'description')),
                'categories' => $this->labels($this->property($offer, 'category')),
                'deadline' => $this->date($this->property($offer, 'availabilityEnds')),
                'requirements' => $this->mapRequirements($this->property($offer, 'demand')),
            ];
            if (array_filter($detail)) {
                $offers[] = $detail;
            }
        }
        return $offers;
    }

    /** @return list<SponsorItem> */
    private function mapItems(mixed $value): array
    {
        $items = [];
        foreach ($this->asList($value) as $item) {
            $detail = [
                'name' => $this->text($this->property($item, 'name')),
                'description' => $this->richText($this->property($item, 'description')),
            ];
            if (array_filter($detail)) {
                $items[] = $detail;
            }
        }
        return $items;
    }

    /** @return list<SponsorImage> */
    private function mapImages(mixed $value): array
    {
        $images = [];
        foreach ($this->asList($value) as $image) {
            $source = $this->text($image);
            if ($source === '') {
                $source = $this->property($image, 'url') ?: $this->property($image, 'contentUrl');
            }
            $src = $this->url($source);
            if ($src !== '') {
                $images[] = ['src' => $src, 'alt' => $this->text($this->property($image, 'description'))];
            }
        }
        return $images;
    }

    /** @return list<SponsorContact> */
    private function mapContacts(mixed $contacts): array
    {
        $contactDetails = [];
        foreach ($this->asList($contacts) as $contact) {
            $name = $this->text($this->property($contact, 'name'));
            $role = $this->text($this->property($contact, 'role'));
            $email = $this->text($this->property($contact, 'email'));
            $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
            $telephone = $this->text($this->property($contact, 'telephone'));
            $telephoneHref = (string) preg_replace('/[^+0-9]/', '', $telephone);
            if ($name !== '' || $role !== '' || $email !== '' || $telephone !== '') {
                $contactDetails[] = [
                    'name' => $name,
                    'role' => $role,
                    'email' => $email,
                    'telephone' => $telephone,
                    'telephoneHref' => $telephoneHref,
                ];
            }
        }
        return $contactDetails;
    }

    /** @return list<SponsorRequirement> */
    private function mapRequirements(mixed $requirements): array
    {
        $details = [];
        foreach ($this->asList($requirements) as $demand) {
            $proposals = [];
            foreach ($this->asList($this->property($demand, 'keywords')) as $keyword) {
                $termSet = $this->property($keyword, 'inDefinedTermSet');
                if ($this->property($termSet, 'name') === 'proposal_for_counter_performance') {
                    foreach ($this->labels($keyword) as $label) {
                        $proposals[] = $label;
                    }
                }
            }
            $detail = [
                'name' => $this->text($this->property($demand, 'name')),
                'description' => $this->richText($this->property($demand, 'description')),
                'counterPerformance' => $proposals,
            ];
            if (array_filter($detail)) {
                $details[] = $detail;
            }
        }
        return $details;
    }

    /** Schema properties may be native objects or stored associative arrays. */
    private function property(mixed $object, string $key): mixed
    {
        if ($object instanceof BaseType) {
            return $object->getProperty($key);
        }
        if (is_array($object)) {
            return $object[$key] ?? null;
        }
        return null;
    }

    /**
     * Wrap a single property value; keep a list as-is. An associative schema
     * array is one object, not a list of its property values.
     *
     * @return list<mixed>
     */
    private function asList(mixed $value): array
    {
        if ($value === null || $value === false || $value === '') {
            return [];
        }
        return is_array($value) && array_is_list($value) ? $value : [$value];
    }

    private function text(mixed $value): string
    {
        return is_string($value) || is_int($value) || is_float($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private function labels(mixed $value): array
    {
        $labels = [];
        foreach ($this->asList($value) as $item) {
            $label = $this->text($item);
            if ($label === '') {
                $label = $this->text($this->property($item, 'name'));
            }
            if ($label !== '') {
                $labels[] = $label;
            }
        }
        return $labels;
    }

    private function richText(mixed $value): string
    {
        $parts = [];
        foreach ($this->asList($value) as $item) {
            $part = $this->text($item);
            if ($part === '') {
                $part = $this->text($this->property($item, 'text'));
            }
            if ($part !== '') {
                $parts[] = $part;
            }
        }
        $html = implode("\n", $parts);
        if ($html === '') {
            return '';
        }
        $safe = $this->wpService->wpKsesPost($this->wpService->wpautop($html));
        return trim(strip_tags($safe)) !== '' ? $safe : '';
    }

    private function url(mixed $value): string
    {
        $url = $this->text($value);
        if (!in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            return '';
        }
        return $this->wpService->escUrlRaw($url, ['http', 'https']);
    }

    private function date(mixed $value): string
    {
        $timezone = $this->wpService->wpTimezone();
        if (!$value instanceof DateTimeInterface) {
            if (!is_string($value) || trim($value) === '') {
                return '';
            }
            try {
                // Match the existing WordPress import sanitizer's UTC interpretation of naive dates.
                // Explicit source offsets still take precedence. Do not reinterpret older UTC metadata.
                $value = new DateTimeImmutable($value, new DateTimeZone('UTC'));
            } catch (\Exception) {
                return '';
            }
        }
        $format = $this->wpService->getOption('date_format') . ' H:i';
        return $this->wpService->wpDate($format, $value->getTimestamp(), $timezone);
    }
}
