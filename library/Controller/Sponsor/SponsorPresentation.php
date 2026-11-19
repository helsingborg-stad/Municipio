<?php

namespace Municipio\Controller\Sponsor;

use DateTimeImmutable;
use DateTimeZone;
use Municipio\Schema\SponsorDemand;
use Municipio\Schema\SponsorOffer;
use WpService\WpService;

class SponsorPresentation
{
    public function __construct(private WpService $wpService)
    {
    }

    public function map(SponsorDemand|SponsorOffer $schema): array
    {
        $isDemand = $schema instanceof SponsorDemand;
        $data = $schema->toArray();
        $organization = ($isDemand ? $data['@reverse']['seeks'] : $data['offeredBy']) ?? [];
        $contact = $organization['contactPoint'] ?? [];
        $telephone = $contact['telephone'] ?? '';
        $url = $organization['url'] ?? '';

        return [
            'description' => $this->richText($data['description'] ?? ''),
            'supportDate' => $this->date($data[$isDemand ? 'sponsor:supportNeededBy' : 'sponsor:sponsorshipDate'] ?? ''),
            'location' => $data['sponsor:supportLocation'] ?? '',
            'resources' => $this->terms($data['keywords'] ?? [], 'resource'),
            'activities' => $isDemand ? $this->terms($organization['keywords'] ?? [], 'activity') : '',
            'preferences' => $data['sponsor:sponsorPreferences'] ?? '',
            'practicalInformation' => $data['sponsor:practicalInformation'] ?? '',
            'counterPerformance' => $data[$isDemand ? 'sponsor:counterPerformance' : 'sponsor:requestedCounterPerformance'] ?? '',
            'organization' => [
                'name' => $organization['name'] ?? '',
                'description' => $this->richText($organization['description'] ?? ''),
                'url' => $this->wpService->escUrlRaw($url, ['http', 'https']),
            ],
            'contacts' => $contact ? [[
                'name' => $contact['name'] ?? '',
                'role' => $contact['description'] ?? '',
                'email' => filter_var($contact['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '',
                'telephone' => $telephone,
                'telephoneHref' => preg_replace('/[^+0-9]/', '', $telephone),
            ]] : [],
        ];
    }

    private function terms(array $terms, string $group): string
    {
        return implode(', ', array_column(array_filter(
            $terms,
            static fn($term) => ($term['inDefinedTermSet']['name'] ?? null) === $group,
        ), 'name'));
    }

    private function richText(string $value): string
    {
        return $value === '' ? '' : $this->wpService->wpKsesPost($this->wpService->wpautop($value));
    }

    private function date(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $dateOnly = preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value);
        $timezone = $this->wpService->wpTimezone();
        try {
            $date = new DateTimeImmutable($value, $dateOnly ? $timezone : new DateTimeZone('UTC'));
        } catch (\Exception) {
            return '';
        }

        $format = $this->wpService->getOption('date_format') . ($dateOnly ? '' : ' H:i');
        return $this->wpService->wpDate($format, $date->getTimestamp(), $timezone);
    }
}
