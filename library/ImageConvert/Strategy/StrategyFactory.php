<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Strategy\ConversionStrategyInterface;
use Municipio\ImageConvert\Strategy\RuntimeConversionStrategy;
use Municipio\ImageConvert\Strategy\BackgroundConversionStrategy;
use Municipio\ImageConvert\Strategy\WpCliConversionStrategy;
use Municipio\ImageConvert\Strategy\MixedConversionStrategy;
use Municipio\ImageConvert\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetCurrentUserId;
use WpService\Contracts\UserCan;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPost;

enum ConversionStrategy: string
{
    case RUNTIME = 'runtime';
    case BACKGROUND = 'background';
    case MIXED = 'mixed';
}

/**
 * Strategy Factory
 * 
 * Creates and manages conversion strategies based on configuration.
 * Supports strategy selection via MUNICIPIO_IMAGE_CONVERT_STRATEGY constant.
 */
class StrategyFactory
{
    // Default strategy if not specified
    private const DEFAULT_STRATEGY = ConversionStrategy::RUNTIME;

    public function __construct(
        private WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter&DoAction&GetCurrentUserId&UserCan&GetPostMeta&GetPost $wpService,
        private ImageConvertConfig $config,
        private ConversionCache $conversionCache
    ) {
    }

    /**
     * Create a conversion strategy based on configuration
     * 
     * @return ConversionStrategyInterface
     * @throws \InvalidArgumentException If the strategy is not supported
     */
    public function createStrategy(): ConversionStrategyInterface
    {
        $strategy = $this->getSelectedStrategy();
        
        return match ($strategy) {
            ConversionStrategy::RUNTIME => new RuntimeConversionStrategy(
                $this->wpService,
                $this->config,
                $this->conversionCache
            ),
            ConversionStrategy::BACKGROUND => new BackgroundConversionStrategy(
                $this->wpService,
                $this->config,
                $this->conversionCache
            ),
            ConversionStrategy::MIXED => new MixedConversionStrategy(
                $this->wpService,
                $this->config,
                $this->conversionCache,
                new RuntimeConversionStrategy(
                    $this->wpService,
                    $this->config,
                    $this->conversionCache
                ),
                new BackgroundConversionStrategy(
                    $this->wpService,
                    $this->config,
                    $this->conversionCache
                )
            ),
        };
    }

    /**
     * Get the selected strategy from configuration
     * 
     * @return ConversionStrategy
     */
    public function getSelectedStrategy(): ConversionStrategy
    {
        // Check if constant is defined
        if (defined('MUNICIPIO_IMAGE_CONVERT_STRATEGY')) {
            $strategy = constant('MUNICIPIO_IMAGE_CONVERT_STRATEGY');
            
            // Validate the strategy
            $enumStrategy = ConversionStrategy::tryFrom($strategy);
            if ($enumStrategy !== null) {
                return $enumStrategy;
            }
            
            // Log warning for invalid strategy
            error_log(
                "Invalid MUNICIPIO_IMAGE_CONVERT_STRATEGY value: {$strategy}. " .
                "Falling back to default strategy: " . self::DEFAULT_STRATEGY->value
            );
        }
        
        return self::DEFAULT_STRATEGY;
    }

    /**
     * Get list of supported strategies
     * 
     * @return array<ConversionStrategy>
     */
    public function getSupportedStrategies(): array
    {
        return ConversionStrategy::cases();
    }

    /**
     * Check if a strategy is supported
     * 
     * @param ConversionStrategy $strategy
     * @return bool
     */
    public function isStrategySupported(ConversionStrategy $strategy): bool
    {
        return in_array($strategy, ConversionStrategy::cases(), true);
    }
}