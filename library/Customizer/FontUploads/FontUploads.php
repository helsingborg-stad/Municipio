<?php

namespace Municipio\Customizer\FontUploads;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;

/**
 * Font uploads
 * 
 * This class allows you to upload custom fonts to the WordPress media library and use them in the customizer.
 * 
 * @source https://github.com/ouun/kirki-module-fonts_upload
 * License: MIT
 */
class FontUploads implements Hookable {

	public $allowedMimes = array(
		'woff'  => 'application/font-woff',
		'woff2' => 'application/font-woff2',
	);

	public function __construct(private WpService $wpService) {}

	public function addHooks(): void
	{
		$this->wpService->addFilter('upload_mimes', array($this, 'addFontMimes'), 1, 1);
		$this->wpService->addFilter('kirki_fonts_standard_fonts', array($this, 'addUploadedFontsToStack'), 1, 100);
		$this->wpService->addAction('kirki_dynamic_css', array($this, 'getUploadedFontsCss'), 0, 20);
	}

	/**
	 * Get uploaded fonts
	 *
	 * @return array
	 */
	public function getUploadedFonts(): array
	{
		$fontAttachments = new \WP_Query( array(
			'post_type'      => 'attachment',
			'posts_per_page' => 50,
			'post_status'    => ['publish', 'inherit'],
			'post_mime_type' => $this->allowedMimes
		));

		$fonts = [];

		foreach ($fontAttachments->posts as $font) {
			if ($url = $this->wpService->wpGetAttachmentUrl($font->ID)) {
				$fonts[$font->post_name] = [
					'name' => $font->post_title ?? __('Untitled Font', 'municipio'),
					'type' => $this->wpService->wpCheckFiletype($url)['ext'] ?? null,
					'url'  => str_replace($this->wpService->homeUrl(), '', $url),
				];
			}
		}

		return $fonts;
	}

	/**
	 * Add font mimes
	 *
	 * @param array $mimes
	 * @return array
	 */
	public function addFontMimes(array $mimes) : array
	{
		foreach ($this->allowedMimes as $ext => $mime) {
			$mimes[$ext] = $mime;
		}
		return $mimes;
	}

	/**
	 * Add uploaded fonts to stack
	 *
	 * @param array $fonts
	 * @return array
	 */
	public function addUploadedFontsToStack(array $fonts): array 
	{
		foreach (self::getUploadedFonts() as $font) {
			$fonts[$font['name'] ] = array(
				'label' => $font['name'],
				'stack' => $font['name'],
			);
		}
		return $fonts;
	}

	/**
	 * Get uploaded fonts css
	 *
	 * @return void
	 */
	public function getUploadedFontsCss(): void
	{
		foreach (self::getUploadedFonts() as $font) {
			echo $this->wpService->wpStripAllTags( "@font-face{font-display:swap;font-family:\"{$font['name']}\";src:url(\"{$font['url']}\");format(\"{$font['type']}\");}" );
		}
	}
}
