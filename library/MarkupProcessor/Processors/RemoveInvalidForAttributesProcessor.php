<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Remove for attributes whose referenced IDs are absent from the final markup.
 */
class RemoveInvalidForAttributesProcessor implements MarkupProcessorInterface
{
    private const TOKENS = '~<!--.*?-->|<(script|style|textarea|title)\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*?>.*?</\1\s*>|<(?:(?:"[^"]*")|(?:\'[^\']*\')|[^\'">])*?>~is';

    private const ATTRIBUTE = '/\s%s\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';

    public function process(string $markup): string
    {
        if (stripos($markup, 'for=') === false && !preg_match('/\sfor\s*=/i', $markup)) {
            return $markup;
        }

        preg_match_all(self::TOKENS, $markup, $tokens, PREG_OFFSET_CAPTURE);
        $ids = [];

        foreach ($tokens[0] as [$token]) {
            if (!$this->isElementTag($token)) {
                continue;
            }

            // Raw-text elements are one token; inspect only their opening tag.
            preg_match('~^<(?:(?:"[^"]*")|(?:\'[^\']*\')|[^\'">])*?>~s', $token, $openingTag);
            $token = $openingTag[0] ?? $token;
            $id = $this->attributeValue($token, 'id');
            if ($id !== null && $id !== '') {
                $ids[$id] = true;
            }
        }

        return preg_replace_callback(
            self::TOKENS,
            function (array $matches) use ($ids): string {
                $tag = $matches[0];
                if (!$this->isElementTag($tag) || preg_match('/^<(?:script|style|textarea|title)\b/i', $tag)) {
                    return $tag;
                }

                $for = $this->attributeValue($tag, 'for');
                if ($for === null) {
                    return $tag;
                }

                preg_match('/^<\s*([a-z][\w:-]*)/i', $tag, $element);
                $targets = strtolower($element[1] ?? '') === 'output'
                    ? preg_split('/[\x20\t\n\f\r]+/', trim($for))
                    : [$for];

                foreach ($targets as $target) {
                    if ($target === '' || !isset($ids[$target])) {
                        return preg_replace(sprintf(self::ATTRIBUTE, 'for'), '', $tag, 1) ?? $tag;
                    }
                }

                return $tag;
            },
            $markup,
        ) ?? $markup;
    }

    private function isElementTag(string $token): bool
    {
        return preg_match('/^<[a-z][\w:-]*\b/i', $token) === 1;
    }

    private function attributeValue(string $tag, string $name): ?string
    {
        if (!preg_match(sprintf(self::ATTRIBUTE, preg_quote($name, '/')), $tag, $matches, PREG_UNMATCHED_AS_NULL)) {
            return null;
        }

        $value = $matches[1] ?? $matches[2] ?? $matches[3] ?? '';

        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
