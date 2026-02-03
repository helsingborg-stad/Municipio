<?php

namespace Municipio\Controller\Archive;

/**
 * Factory for creating async configuration arrays from various sources.
 *
 * Follows Open/Closed Principle - extensible by adding new extractors without modifying this class.
 * Follows Dependency Inversion Principle - depends on abstractions (interfaces) not concretions.
 * Follows Single Responsibility Principle - only responsible for orchestrating the build process.
 */
class AsyncConfigBuilderFactory
{
    private AsyncConfigBuilderInterface $builder;

    /**
     * @param AsyncConfigBuilderInterface $builder The builder to use for creating configs
     */
    public function __construct(AsyncConfigBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Create async configuration from posts list config DTO and data.
     *
     * @param mixed $postsListConfigDTO The posts list configuration DTO
     * @param array $postsListData The posts list data array
     * @param bool $isAsync Whether this is an async request
     * @param array|null $sourceAttributes Optional source attributes to preserve
     * @return array The built async configuration
     */
    public function create(
        $postsListConfigDTO,
        array $postsListData,
        bool $isAsync = true,
        ?array $sourceAttributes = null
    ): array {
        // Reset builder to clean state
        $this->builder->reset();

        // Set source attributes if provided (preserves filter configs, design, etc.)
        if ($sourceAttributes !== null) {
            $sourceExtractor = new SourceAttributesExtractor($sourceAttributes);
            $sourceData = $sourceExtractor->extract();
            if (isset($sourceData['sourceAttributes'])) {
                $this->builder->setSourceAttributes($sourceData['sourceAttributes']);
            }
        }

        // Extract data using specialized extractors
        $extractors = [
            new AppearanceConfigExtractor($postsListConfigDTO->getAppearanceConfig()),
            new GetPostsConfigExtractor($postsListConfigDTO->getGetPostsConfig()),
            new PostsListDataExtractor($postsListData),
        ];

        // Apply extracted data to builder
        $this->applyExtractedData($extractors);

        // Set additional properties
        $this->builder
            ->setQueryVarsPrefix($postsListConfigDTO->getQueryVarsPrefix() ?? '')
            ->setIsAsync($isAsync);

        return $this->builder->build();
    }

    /**
     * Apply data from extractors to the builder.
     *
     * @param AsyncConfigExtractorInterface[] $extractors Array of extractors
     * @return void
     */
    private function applyExtractedData(array $extractors): void
    {
        foreach ($extractors as $extractor) {
            $data = $extractor->extract();

            foreach ($data as $key => $value) {
                $this->applyDataToBuilder($key, $value);
            }
        }
    }

    /**
     * Apply a single data point to the builder using the appropriate setter.
     *
     * @param string $key The data key
     * @param mixed $value The data value
     * @return void
     */
    private function applyDataToBuilder(string $key, $value): void
    {
        $setterMap = [
            'queryVarsPrefix' => 'setQueryVarsPrefix',
            'id' => 'setId',
            'postType' => 'setPostType',
            'dateSource' => 'setDateSource',
            'dateFormat' => 'setDateFormat',
            'numberOfColumns' => 'setNumberOfColumns',
            'postsPerPage' => 'setPostsPerPage',
            'paginationEnabled' => 'setPaginationEnabled',
            'asyncId' => 'setAsyncId',
        ];

        if (isset($setterMap[$key]) && $value !== null) {
            $setter = $setterMap[$key];
            $this->builder->$setter($value);
        }
    }

    /**
     * Static factory method for backward compatibility.
     *
     * @deprecated Use the instance method create() with dependency injection instead
     * @param mixed $postsListConfigDTO The posts list configuration DTO
     * @param array $postsListData The posts list data array
     * @param bool $isAsync Whether this is an async request
     * @param array|null $sourceAttributes Optional source attributes to preserve
     * @return array The built async configuration
     */
    public static function fromConfigs(
        $postsListConfigDTO,
        $postsListData,
        bool $isAsync = true,
        ?array $sourceAttributes = null
    ): array {
        $factory = new self(new AsyncConfigBuilder());
        return $factory->create($postsListConfigDTO, $postsListData, $isAsync, $sourceAttributes);
    }
}
