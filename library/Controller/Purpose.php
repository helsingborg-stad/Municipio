<?php 

namespace Municipio;

interface Purpose  {
    
    public function setStructuredData(array $structuredData = [], string $postType, int $postId = null) : array;
    
}
