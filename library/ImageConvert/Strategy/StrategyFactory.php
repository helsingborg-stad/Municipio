<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Strategy\ConversionStrategyInterface;
use Municipio\ImageConvert\Strategy\RuntimeConversionStrategy;
use Municipio\ImageConvert\Cache\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\ImageProcessor;
use Municipio\ImageConvert\Logging\Log;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetCurrentUserId;
use WpService\Contracts\UserCan;
use WpService\Contracts\GetPost;

enum ConversionStrategy: string
{
    case RUNTIME = 'runtime';
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
        private WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter&DoAction&GetCurrentUserId&UserCan&GetPost $wpService,
        private ImageConvertConfig $config,
        private ConversionCache $conversionCache,
        private Log $log
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
        
        // Create shared image processor
        $imageProcessor = new ImageProcessor(
            $this->wpService,
            $this->config,
            $this->conversionCache,
            $this->log
        );
        
        return match ($strategy) {
            ConversionStrategy::RUNTIME => new RuntimeConversionStrategy(
                $imageProcessor
            )
        };
    }

    /**
     * Get the selected strategy from configuration
     * 
     * @return ConversionStrategy
     */
    public function getSelectedStrategy(): ConversionStrategy
    {
        if (defined('MUNICIPIO_IMAGE_CONVERT_STRATEGY')) {
            $strategy       = constant('MUNICIPIO_IMAGE_CONVERT_STRATEGY');
            $enumStrategy   = ConversionStrategy::tryFrom($strategy);

            if ($enumStrategy !== null) {
                return $enumStrategy;
            }
            
            $this->log->log(
                $this,
                "Invalid MUNICIPIO_IMAGE_CONVERT_STRATEGY value: {$strategy}. Falling back to default strategy: " . self::DEFAULT_STRATEGY->value,
                'warning',
                ['provided_value' => $strategy]
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