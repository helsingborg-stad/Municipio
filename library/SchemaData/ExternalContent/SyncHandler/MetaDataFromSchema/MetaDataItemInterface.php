<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema;

interface MetaDataItemInterface {
    public function getKey(): string;
    public function getValue(): mixed;
}