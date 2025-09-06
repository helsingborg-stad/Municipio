<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Strategy\ConversionStrategyInterface;
use Municipio\ImageConvert\Strategy\RuntimeConversionStrategy;
use Municipio\ImageConvert\Strategy\BackgroundConversionStrategy;
use Municipio\ImageConvert\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoAction;

/**
 * Strategy Factory
 * 
 * Creates and manages conversion strategies based on configuration.
 * Supports strategy selection via MUNICIPIO_IMAGE_CONVERT_STRATEGY constant.
 */
class StrategyFactory
{
    // Strategy constants
    public const STRATEGY_RUNTIME = 'runtime';
    public const STRATEGY_BACKGROUND = 'background';
    
    // Default strategy if not specified
    private const DEFAULT_STRATEGY = self::STRATEGY_RUNTIME;

    public function __construct(
        private WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter&DoAction $wpService,
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
        $strategyName = $this->getSelectedStrategy();
        
        return match ($strategyName) {
            self::STRATEGY_RUNTIME => new RuntimeConversionStrategy(
                $this->wpService,
                $this->config,
                $this->conversionCache
            ),
            self::STRATEGY_BACKGROUND => new BackgroundConversionStrategy(
                $this->wpService,
                $this->conversionCache
            ),
            default => throw new \InvalidArgumentException(
                "Unsupported conversion strategy: {$strategyName}. " .
                "Supported strategies: " . implode(', ', $this->getSupportedStrategies())
            )
        };
    }

    /**
     * Get the selected strategy name from configuration
     * 
     * @return string
     */
    public function getSelectedStrategy(): string
    {
        // Check if constant is defined
        if (defined('MUNICIPIO_IMAGE_CONVERT_STRATEGY')) {
            $strategy = constant('MUNICIPIO_IMAGE_CONVERT_STRATEGY');
            
            // Validate the strategy
            if (in_array($strategy, $this->getSupportedStrategies(), true)) {
                return $strategy;
            }
            
            // Log warning for invalid strategy
            error_log(
                "Invalid MUNICIPIO_IMAGE_CONVERT_STRATEGY value: {$strategy}. " .
                "Falling back to default strategy: " . self::DEFAULT_STRATEGY
            );
        }
        
        return self::DEFAULT_STRATEGY;
    }

    /**
     * Get list of supported strategies
     * 
     * @return array
     */
    public function getSupportedStrategies(): array
    {
        return [
            self::STRATEGY_RUNTIME,
            self::STRATEGY_BACKGROUND
        ];
    }

    /**
     * Check if a strategy is supported
     * 
     * @param string $strategyName
     * @return bool
     */
    public function isStrategySupported(string $strategyName): bool
    {
        return in_array($strategyName, $this->getSupportedStrategies(), true);
    }
}