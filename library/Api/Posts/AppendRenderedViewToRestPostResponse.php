<?php

namespace Municipio\Api\Posts;

use Municipio\Customizer\Sections\Header\Appearance;
use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use Municipio\PostObject\PostObjectRenderer\Appearances\Appearance as AppearancesAppearance;
use Municipio\PostObject\PostObjectRenderer\PostObjectRendererFactoryInterface;
use WP_REST_Request;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPost;
use WpService\Contracts\RegisterRestField;

/**
 * Append the rendered view to the post response.
 */
class AppendRenderedViewToRestPostResponse implements Hookable
{
    private const FIELD_NAME = 'rendered';
    private const PARAM_NAME = 'appearance';

    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectRendererFactoryInterface $rendererFactory,
        private AddFilter&RegisterRestField&GetPost $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('rest_api_init', [$this, 'registerRestField']);
    }

    /**
     * Register the rest field.
     */
    public function registerRestField(): void
    {
        $this->wpService->registerRestField('post', self::FIELD_NAME, [
            'get_callback' => [$this, 'appendRenderedView'],
            'schema'       => [
                'description' => __('The rendered PostObject'),
                'type'        => ['string', 'null'],
                'context'     => ['view'],
            ],
        ]);
    }

    /**
     * Append the rendered view to the post response.
     *
     * @param array $data
     * @param string $fieldName
     * @param WP_REST_Request $request
     * @return string|null The rendered view or null if the appearance is not supported.
     */
    public function appendRenderedView(array $data, string $fieldName, WP_REST_Request $request): ?string
    {
        $queryParams = $request->get_query_params();
        $appearance  = AppearancesAppearance::tryFrom($queryParams[self::PARAM_NAME] ?? null);

        if (empty($appearance)) {
            return null;
        }

        $rendererInstance = $this->rendererFactory->create($appearance);
        $post             = $this->wpService->getPost($data['id']);
        $postObject       = Post::preparePostObject($post);

        return $rendererInstance->render($postObject);
    }
}
