<?php

namespace Municipio\Api\Customizer;

use Municipio\Api\RestApiEndpoint;
use Municipio\Customizer\DesignLibrarySettingPolicy;
use WP_Error;
use WP_Http;
use WP_REST_Response;
use WP_REST_Request;
use WP_REST_Server;

class DesignLibrary extends RestApiEndpoint
{
	private const NAMESPACE = 'municipio/v1';
	private const ROUTE_CONFIG = 'design-library/config';
	private const ROUTE_IMPORT_PROXY = 'design-library/import';

	private const SETTINGS_EXCLUDED_FROM_EXPORT = [
		'load_design',
		'exclude_load_design',
		'load_design_site_url',
		'municipio_font_catalog_uploaded_fonts',
		'municipio_font_catalog_migrated',
	];

	public function handleRegisterRestRoute(): bool
	{
		$configRouteRegistered = register_rest_route(self::NAMESPACE, self::ROUTE_CONFIG, [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'handleRequest'],
			'permission_callback' => [$this, 'permissionCallback'],
		]);

		$importRouteRegistered = register_rest_route(self::NAMESPACE, self::ROUTE_IMPORT_PROXY, [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'handleImportRequest'],
			'permission_callback' => [$this, 'importPermissionCallback'],
			'args'                => [
				'source' => [
					'description' => __('Source Municipio site URL.', 'municipio'),
					'type' => 'string',
					'required' => true,
				],
			],
		]);

		return $configRouteRegistered && $importRouteRegistered;
	}

	public function handleRequest(WP_REST_Request $request)
	{
		$response = rest_ensure_response($this->getSiteConfig());

		if ($response instanceof WP_REST_Response) {
			$response->header('Access-Control-Allow-Origin', '*');
			$response->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
			$response->header('Access-Control-Allow-Headers', 'Content-Type, X-WP-Nonce, Authorization');
		}

		return $response;
	}

	public function permissionCallback(): bool
	{
		return true;
	}

	/**
	 * Proxy import callback. Fetches remote design config server-side to avoid browser CORS issues.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function handleImportRequest(WP_REST_Request $request)
	{
		$sourceSiteUrl = $request->get_param('source');

		if (!is_string($sourceSiteUrl) || trim($sourceSiteUrl) === '') {
			return new WP_Error(
				'municipio_design_import_missing_source',
				__('Missing source URL.', 'municipio'),
				['status' => WP_Http::BAD_REQUEST],
			);
		}

		$normalizedSourceUrl = $this->normalizeSourceSiteUrl($sourceSiteUrl);

		if ($normalizedSourceUrl === null) {
			return new WP_Error(
				'municipio_design_import_invalid_source',
				__('Invalid source URL.', 'municipio'),
				['status' => WP_Http::BAD_REQUEST],
			);
		}

		$cacheBust = $request->get_param('cache-bust');
		$cacheBust = is_string($cacheBust) && $cacheBust !== '' ? $cacheBust : uniqid('import-', true);

		$remoteConfigUrl = $this->buildRemoteConfigUrl($normalizedSourceUrl, $cacheBust);
		$remoteResponse = wp_remote_get($remoteConfigUrl, [
			'timeout' => 10,
			'headers' => [
				'Accept' => 'application/json',
			],
		]);

		if (is_wp_error($remoteResponse)) {
			return new WP_Error(
				'municipio_design_import_fetch_failed',
				__('Unable to fetch design data from the source site.', 'municipio'),
				['status' => WP_Http::BAD_GATEWAY],
			);
		}

		$statusCode = (int) wp_remote_retrieve_response_code($remoteResponse);
		$rawBody = (string) wp_remote_retrieve_body($remoteResponse);

		if ($statusCode < 200 || $statusCode >= 300) {
			return new WP_Error(
				'municipio_design_import_remote_status',
				__('The source site returned an invalid response.', 'municipio'),
				['status' => WP_Http::BAD_GATEWAY],
			);
		}

		$decodedBody = json_decode($rawBody, true);

		if (!is_array($decodedBody)) {
			return new WP_Error(
				'municipio_design_import_invalid_payload',
				__('The source site returned invalid design data.', 'municipio'),
				['status' => WP_Http::BAD_GATEWAY],
			);
		}

		return rest_ensure_response($decodedBody);
	}

	/**
	 * Restrict proxy import to authenticated users with Customizer capability.
	 */
	public function importPermissionCallback(): bool
	{
		return is_user_logged_in() && current_user_can('customize');
	}

	private function normalizeSourceSiteUrl(string $sourceSiteUrl): ?string
	{
		$trimmedUrl = trim($sourceSiteUrl);

		if (!preg_match('/^https?:\/\//i', $trimmedUrl)) {
			$trimmedUrl = 'https://' . $trimmedUrl;
		}

		if (!wp_http_validate_url($trimmedUrl)) {
			return null;
		}

		$scheme = wp_parse_url($trimmedUrl, PHP_URL_SCHEME);
		if (!is_string($scheme) || !in_array(strtolower($scheme), ['http', 'https'], true)) {
			return null;
		}

		return $trimmedUrl;
	}

	private function buildRemoteConfigUrl(string $sourceSiteUrl, string $cacheBust): string
	{
		$remoteConfigUrl = rtrim($sourceSiteUrl, '/') . '/wp-json/municipio/v1/design-library/config';
		return add_query_arg('cache-bust', $cacheBust, $remoteConfigUrl);
	}

	/**
	 * Build a design import payload for this site.
	 *
	 * @return array<string, mixed>
	 */
	protected function getSiteConfig(): array
	{
		return [
			'uuid' => md5(ABSPATH . get_home_url()),
			'website' => get_home_url(),
			'name' => get_bloginfo('name'),
			'dbVersion' => (int) get_option('municipio_db_version', 0),
			'municipioVersion' => wp_get_theme()->get('Version'),
			'allowedSettingKeys' => DesignLibrarySettingPolicy::getAllowedExactKeys(),
			'allowedSettingKeyPrefixes' => DesignLibrarySettingPolicy::getAllowedPrefixes(),
			'mods' => $this->getShareableThemeMods(),
			'css' => wp_get_custom_css() ?? false,
		];
	}

	/**
	 * Get theme mods that can be safely imported by the design tool.
	 *
	 * @return array<string, mixed>
	 */
	private function getShareableThemeMods(): array
	{
		$mods = get_theme_mods();

		if (!is_array($mods)) {
			return [];
		}

		$sharedMods = [];

		foreach ($mods as $key => $mod) {
			if (!is_string($key)) {
				continue;
			}

			if (in_array($key, self::SETTINGS_EXCLUDED_FROM_EXPORT, true)) {
				continue;
			}

			if (!DesignLibrarySettingPolicy::isAllowedSettingKey($key)) {
				continue;
			}

			$sharedMods[$key] = $mod;
		}

		$activeFontFileUrls = $this->getActiveFontFileUrls();

		if ($activeFontFileUrls !== []) {
			$sharedMods['custom_fonts'] = $activeFontFileUrls;
		}

		return $sharedMods;
	}

	/**
	 * Get file URLs for the font families currently activated in the Font Library.
	 * Fonts present in the Font Library but not activated are intentionally excluded.
	 *
	 * @return array<string, string>
	 */
	private function getActiveFontFileUrls(): array
	{
		$fontFileUrls = [];

		foreach ($this->getActivatedFontFamilies() as $fontFamily) {
			$name = $fontFamily['name'] ?? null;
			$fontFaces = $fontFamily['fontFace'] ?? null;

			if (!is_string($name) || $name === '' || !is_array($fontFaces)) {
				continue;
			}

			$fontFileUrl = $this->getFirstFontFaceSourceUrl($fontFaces);

			if ($fontFileUrl !== null) {
				$fontFileUrls[$name] = $fontFileUrl;
			}
		}

		return $fontFileUrls;
	}

	/**
	 * Get the font families currently activated via the Font Library (stored in Global Styles).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function getActivatedFontFamilies(): array
	{
		if (
			!class_exists(\WP_Theme_JSON_Resolver::class)
			|| !method_exists(\WP_Theme_JSON_Resolver::class, 'get_user_global_styles_post_id')
		) {
			return [];
		}

		$globalStylesPostId = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

		if (!is_numeric($globalStylesPostId) || (int) $globalStylesPostId <= 0) {
			return [];
		}

		$globalStylesPost = get_post((int) $globalStylesPostId);
		$postContent = is_object($globalStylesPost) && property_exists($globalStylesPost, 'post_content')
			? (string) $globalStylesPost->post_content
			: '';

		$globalStylesData = json_decode($postContent, true);
		$customFontFamilies = $globalStylesData['settings']['typography']['fontFamilies']['custom'] ?? null;

		return is_array($customFontFamilies) ? array_values(array_filter($customFontFamilies, 'is_array')) : [];
	}

	/**
	 * @param array<int, array<string, mixed>> $fontFaces
	 */
	private function getFirstFontFaceSourceUrl(array $fontFaces): ?string
	{
		foreach ($fontFaces as $fontFace) {
			$src = $fontFace['src'] ?? null;
			$sources = is_array($src) ? $src : (is_string($src) ? [$src] : []);

			if ($sources !== [] && is_string($sources[0]) && $sources[0] !== '') {
				return $sources[0];
			}
		}

		return null;
	}
}

