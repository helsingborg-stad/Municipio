<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

/** Builds Apple Wallet pass data from a category template and caller-provided placeholders. */
class ApplePassDataFactory
{
    /**
     * @param ApplePassCardConfig $config Apple pass identifiers.
     * @param ApplePassTemplateLoader $templateLoader Loads category pass templates.
     */
    public function __construct(
        private ApplePassCardConfig $config,
        private ApplePassTemplateLoader $templateLoader,
    ) {}

    /**
     * Creates pass data by filling the category template placeholders.
     *
     * @param string $category Category slug selecting the pass template.
     * @param array<string, string> $placeholders Placeholder replacements keyed by placeholder, e.g. "{{name}}".
     * @return array Pass data.
     * @throws \RuntimeException When the category template cannot be loaded.
     */
    public function create(string $category, array $placeholders): array
    {
        return $this->replacePlaceholders($this->templateLoader->loadPassDefinition($category), [
            '{{APPLE_PASS_TYPE_IDENTIFIER}}' => $this->config->getPassTypeIdentifier(),
            '{{APPLE_TEAM_ID}}' => $this->config->getTeamIdentifier(),
            ...$placeholders,
        ]);
    }

    /**
     * Replaces placeholders in every string value of a decoded template, leaving keys untouched.
     *
     * @param array $data Decoded template data.
     * @param array<string, string> $placeholders Placeholder replacements keyed by placeholder.
     * @return array Data with placeholders replaced.
     */
    private function replacePlaceholders(array $data, array $placeholders): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->replacePlaceholders($value, $placeholders);
            } elseif (is_string($value)) {
                $data[$key] = strtr($value, $placeholders);
            }
        }

        return $data;
    }
}
