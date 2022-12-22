<?php 

namespace Municipio;

interface Purpose  {
    
    public function setStructuredData(array $structuredData = [], string $postType = null, int $postId = null) : array;
    
}
