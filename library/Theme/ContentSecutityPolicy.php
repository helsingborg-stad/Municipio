<?php 
/**
 * Interface for Cacheable classes.
 */
interface Cacheable {
    public function getFromCache();
    public function setToCache($data);
}

/**
 * Class for generating the hashes for enqueued scripts.
 */
class ScriptHashGenerator {
    /**
     * Generate hashes for all enqueued scripts.
     *
     * @return array Array of script hashes.
     */
    public function generate() {
        $scriptHashes = [];
        global $wpScripts;

        foreach ($wpScripts->queue as $handle) {
            $script = $wpScripts->registered[$handle];
            if (isset($script->src)) {
                $scriptContent = @file_get_contents($script->src);
                if ($scriptContent !== false) {
                    $hash = base64_encode(hash('sha256', $scriptContent, true));
                    $scriptHashes[] = "'sha256-$hash'";
                }
            }
        }

        return $scriptHashes;
    }
}

/**
 * Class to manage caching of CSP script hashes.
 */
class CacheHandler implements Cacheable {
    /**
     * Get the cached script hashes.
     *
     * @return mixed Cached script hashes or false if not found.
     */
    public function getFromCache() {
        return wp_cache_get('cspScriptHashes', 'contentSecurityPolicy');
    }

    /**
     * Store the script hashes in cache for a specific duration.
     *
     * @param array $data Script hashes to store.
     */
    public function setToCache($data) {
        wp_cache_set('cspScriptHashes', $data, 'contentSecurityPolicy', 43200); // Cache for 12 hours.
    }
}

/**
 * Class to handle Content Security Policy header generation.
 */
class ContentSecurityPolicyHeader {
    private $hashGenerator;
    private $cacheHandler;

    /**
     * Constructor to inject dependencies.
     * 
     * @param ScriptHashGenerator $hashGenerator
     * @param CacheHandler        $cacheHandler
     */
    public function __construct(ScriptHashGenerator $hashGenerator, CacheHandler $cacheHandler) {
        $this->hashGenerator = $hashGenerator;
        $this->cacheHandler = $cacheHandler;
    }

    /**
     * Add the Content-Security-Policy header with the appropriate hashes for enqueued scripts.
     */
    public function addCspHeader() {
        // Ensure the header is set only on the frontend.
        if (!is_admin()) {
            // Try to get the CSP data from the cache.
            $scriptHashes = $this->cacheHandler->getFromCache();

            // If not found in cache, generate the hashes.
            if ($scriptHashes === false) {
                $scriptHashes = $this->hashGenerator->generate();
                $this->cacheHandler->setToCache($scriptHashes);
            }

            // If there are any hashes, set the Content-Security-Policy header.
            if (!empty($scriptHashes)) {
                $cspHeader = "Content-Security-Policy: script-src 'self' " . implode(' ', $scriptHashes);
                header($cspHeader);
            }
        }
    }
}

/**
 * Main class to initialize and trigger the CSP header process.
 */
class ContentSecurityPolicyEnforcer {
    private $cspHeader;

    /**
     * Constructor to inject the dependency for CSP header handling.
     * 
     * @param ContentSecurityPolicyHeader $cspHeader
     */
    public function __construct(ContentSecurityPolicyHeader $cspHeader) {
        $this->cspHeader = $cspHeader;
    }

    /**
     * Trigger the CSP header addition on wp_loaded action.
     */
    public function enforceCspHeader() {
        add_action('wp_loaded', [$this->cspHeader, 'addCspHeader']);
    }
}

/**
 * Dependency Injection Container (Basic Simulation for WP)
 */
class DiContainer {
    private $services = [];

    /**
     * Register a service with a callback to instantiate it.
     * 
     * @param string   $name     Service name
     * @param callable $callback Function to instantiate the service
     */
    public function register($name, callable $callback) {
        $this->services[$name] = $callback;
    }

    /**
     * Resolve a service and return its instance.
     * 
     * @param string $name Service name
     * @return mixed Service instance
     */
    public function resolve($name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Service not registered: " . $name);
        }

        return $this->services[$name]($this);
    }
}

/**
 * Instantiate DI container and register services.
 */
$container = new DiContainer();

// Register services in the container
$container->register('scriptHashGenerator', function() {
    return new ScriptHashGenerator();
});

$container->register('cacheHandler', function() {
    return new CacheHandler();
});

$container->register('cspHeader', function($container) {
    $hashGenerator = $container->resolve('scriptHashGenerator');
    $cacheHandler = $container->resolve('cacheHandler');
    return new ContentSecurityPolicyHeader($hashGenerator, $cacheHandler);
});

$container->register('cspEnforcer', function($container) {
    $cspHeader = $container->resolve('cspHeader');
    return new ContentSecurityPolicyEnforcer($cspHeader);
});

// Resolve and enforce CSP
$cspEnforcer = $container->resolve('cspEnforcer');
$cspEnforcer->enforceCspHeader();