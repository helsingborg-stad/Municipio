<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRegistryInterface {
    public function initialize();
    public function getRegisteredPostTypes(): array;
    public function getRegisteredPostType(string $postTypeName): ?object;
    public function getRegisteredTaxonomies(): array;
    public function getRegisteredTaxonomy(string $taxonomyName): ?object;
    public function getRegisteredAttachments(): array;
}