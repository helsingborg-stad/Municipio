<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes;
use WpService\Contracts\AddFilter;

/**
 * Class ConvertImagePropsToImageObjects
 * Ensures that all properties, root or nested, that accept ImageObject or ImageObject[] as type are converted to ImageObject instances.
 * This is particularly useful when dealing with external data sources that may provide image data as simple URLs or strings.
 */
class ConvertImagePropsToImageObjects implements Hookable
{
    /**
     * Cache for schema properties per type.
     * @var array<string, array>
     */
    private array $schemaPropertiesCache = [];

    /**
     * Constructor.
     *
     * @param AddFilter $wpService
     * @param GetSchemaPropertiesWithParamTypes|null $getSchemaPropertiesWithParamTypes
     */
    public function __construct(
        private AddFilter $wpService,
        private GetSchemaPropertiesWithParamTypes $getSchemaPropertiesWithParamTypes = new GetSchemaPropertiesWithParamTypes()
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter(SyncHandler::FILTER_BEFORE, [$this, 'convert']);
    }

    /**
     * Convert image properties to ImageObject instances.
     *
     * @param BaseType[] $schemaObjects
     * @return BaseType[]
     */
    public function convert(array $schemaObjects): array
    {
        return array_map(fn(BaseType $schemaObject) => $this->convertImageProperties($schemaObject), $schemaObjects);
    }

    /**
     * Recursively convert image properties to ImageObject instances.
     *
     * @param BaseType $schemaObject
     * @return BaseType
     */
    private function convertImageProperties(BaseType $schemaObject): BaseType
    {
        $schemaType = get_class($schemaObject);

        // Use cached schema properties if available
        if (!isset($this->schemaPropertiesCache[$schemaType])) {
            $this->schemaPropertiesCache[$schemaType] = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schemaType);
        }
        $schemaProperties = $this->schemaPropertiesCache[$schemaType];

        foreach ($schemaProperties as $propertyName => $paramTypes) {
            $propertyValue = $schemaObject->getProperty($propertyName);

            if ($this->isImageProperty($paramTypes)) {
                $converted = $this->convertToImageObject($propertyValue);
                // Only set property if changed
                if ($converted !== $propertyValue) {
                    $schemaObject->setProperty($propertyName, $converted);
                }
                continue;
            }

            if ($propertyValue instanceof BaseType) {
                $converted = $this->convertImageProperties($propertyValue);
                if ($converted !== $propertyValue) {
                    $schemaObject->setProperty($propertyName, $converted);
                }
            }
        }

        return $schemaObject;
    }

    /**
     * Check if property types include ImageObject or ImageObject[].
     *
     * @param array $paramTypes
     * @return bool
     */
    private function isImageProperty(array $paramTypes): bool
    {
        return in_array('ImageObject', $paramTypes, true) || in_array('ImageObject[]', $paramTypes, true);
    }

    /**
     * Convert value to ImageObject instance(s) if needed.
     *
     * @param mixed $value
     * @return mixed
     */
    private function convertToImageObject(mixed $value): mixed
    {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            return Schema::imageObject()->url($value);
        }

        if (is_array($value) && !empty($value)) {
            return array_map(fn($item) => $this->convertToImageObject($item), $value);
        }

        return $value;
    }
}
