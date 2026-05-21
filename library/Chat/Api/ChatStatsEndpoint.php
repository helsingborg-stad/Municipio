<?php

namespace Municipio\Chat\Api;

use Municipio\Api\RestApiEndpoint;
use WpService\Contracts\__;
use WpService\Contracts\GetOption;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\RestEnsureResponse;
use WpService\Contracts\UpdateOption;

class ChatStatsEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/chat/stats';

    public const OPTION_MESSAGES = 'municipio_chat_stats_messages';
    public const OPTION_LIKED    = 'municipio_chat_stats_liked';
    public const OPTION_DISLIKED = 'municipio_chat_stats_disliked';

    public function __construct(
        private GetOption&UpdateOption&RegisterRestRoute&RestEnsureResponse&__ $wpService,
    ) {
    }

    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRequest'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handleRequest(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $type = $request->get_param('type');

        $optionMap = [
            'message' => self::OPTION_MESSAGES,
            'like'    => self::OPTION_LIKED,
            'dislike' => self::OPTION_DISLIKED,
        ];

        if (!isset($optionMap[$type])) {
            return new \WP_Error('invalid_type', $this->wpService->__('Invalid stat type.', 'municipio'), ['status' => 400]);
        }

        $option  = $optionMap[$type];
        $current = (int) $this->wpService->getOption($option, 0);
        $this->wpService->updateOption($option, $current + 1, false);

        return $this->wpService->restEnsureResponse(['success' => true]);
    }
}
