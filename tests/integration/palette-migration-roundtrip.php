<?php

declare(strict_types=1);

// Run with: php tests/integration/palette-migration-roundtrip.php /path/to/wordpress
// No WordPress database or running site is needed.
$core = $argv[1] ?? '';
if (!is_file($core . '/wp-includes/blocks.php') || !is_file($core . '/wp-includes/class-wp-block-parser.php')) {
    throw new RuntimeException('Pass the path to a WordPress installation.');
}
function apply_filters($name, $value, ...$args) { return $value; }
function wp_json_encode($data, $flags = 0, $depth = 512) { return json_encode($data, $flags, $depth); }
function __($value, $domain = null) { return $value; }
require dirname(__DIR__, 2) . '/vendor/autoload.php';
require $core . '/wp-includes/class-wp-block-parser.php';
require $core . '/wp-includes/blocks.php';

$originalText = "&nbsp;\r\n<!-- nested \"block\" --> \\ literal";
$block = [
    'blockName' => 'acf/manualinput',
    'attrs' => ['data' => ['manual_inputs' => [[
        'custom_background_color' => '#2d2d2d',
        'content' => $originalText,
    ]]]],
    'innerBlocks' => [], 'innerContent' => [], 'innerHTML' => '',
];
$initial = serialize_blocks([$block]);
$saved = $initial;
$wp = new \WpService\Implementations\FakeWpService([
    'getPostTypes' => ['private-article'],
    'getPosts' => static fn(array $args): array => $args['post_type'] === ['private-article'] ? [42] : [],
    'getPostField' => static function () use (&$saved): string { return $saved; },
    'wpSlash' => static fn(string $value): string => addslashes($value),
    // Models the unslashing performed by wp_update_post/wp_insert_post.
    'wpUpdatePost' => static function (array $post) use (&$saved): int {
        $saved = stripslashes($post['post_content']);
        return $post['ID'];
    },
]);
$migration = new \Municipio\Upgrade\V70\Version70($wp);
$migration->upgradeToVersion();
$blocks = parse_blocks($saved);
$row = $blocks[0]['attrs']['data']['manual_inputs'][0];
if ($row['content'] !== $originalText || $row['custom_background_color'] !== 'primary') {
    throw new RuntimeException('Block text or palette role did not survive the WordPress serialization round trip.');
}
$firstSave = $saved;
$migration->upgradeToVersion();
if ($saved !== $firstSave || count($wp->methodCalls['wpUpdatePost'] ?? []) !== 1) {
    throw new RuntimeException('The migration must not rewrite an already-migrated block.');
}

// Both earlier migrations can run on databases restored to older versions.
$container = [
    'blockName' => 'acf/container',
    'attrs' => ['data' => ['color' => '#2d2d2d', 'text_color' => '#ffffff', 'text' => $originalText]],
    'innerBlocks' => [], 'innerContent' => [], 'innerHTML' => '',
];
foreach ([64, 65] as $version) {
    $saved = serialize_blocks([$container]);
    $migration = $version === 64
        ? new \Municipio\Upgrade\V64\Version64($wp, new \AcfService\Implementations\FakeAcfService())
        : new \Municipio\Upgrade\V65\Version65($wp);
    $migration->upgradeToVersion();
    $data = parse_blocks($saved)[0]['attrs']['data'];
    if ($data['text'] !== $originalText || $data['color'] !== 'primary' || array_key_exists('text_color', $data)) {
        throw new RuntimeException("Version {$version} did not preserve block text and migrate the color pair.");
    }
}
echo "WordPress block migration round trip passed.\n";
