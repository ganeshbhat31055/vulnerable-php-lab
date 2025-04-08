<?php
// Load environment variables
function loadEnv() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue; // Skip comments
            
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
        }
    }
}

// Load environment variables
loadEnv();

// Vulnerability Configuration
class VulnerabilityConfig {
    private static $instance = null;
    private $config = [];

    private function __construct() {
        $this->loadConfig();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig() {
        // SQL Injection
        $this->config['sql_injection'] = isset($_ENV['SQL_INJECTION_ENABLED']) ? 
            (int)$_ENV['SQL_INJECTION_ENABLED'] : 0;
        
        // File Upload
        $this->config['file_upload'] = isset($_ENV['FILE_UPLOAD_ENABLED']) ? 
            (int)$_ENV['FILE_UPLOAD_ENABLED'] : 0;
        
        // XSS
        $this->config['xss'] = isset($_ENV['XSS_ENABLED']) ? 
            (int)$_ENV['XSS_ENABLED'] : 0;
        
        // CSRF
        $this->config['csrf'] = isset($_ENV['CSRF_ENABLED']) ? 
            (int)$_ENV['CSRF_ENABLED'] : 0;
    }

    public function isEnabled($vulnerability) {
        return isset($this->config[$vulnerability]) && $this->config[$vulnerability] === 1;
    }

    public function getAllConfig() {
        return $this->config;
    }
}

// Helper function to check if a vulnerability is enabled
function isVulnerabilityEnabled($vulnerability) {
    return VulnerabilityConfig::getInstance()->isEnabled($vulnerability);
} 