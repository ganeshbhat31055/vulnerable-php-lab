<?php
// Prevent multiple inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

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
            $this->config['sql_injection'] = isset($_ENV['SQL_INJECTION']) ? 
                ($_ENV['SQL_INJECTION'] === 'true') : false;
            
            // File Upload
            $this->config['file_upload'] = isset($_ENV['FILE_UPLOAD']) ? 
                ($_ENV['FILE_UPLOAD'] === 'true') : false;
            
            // XSS
            $this->config['xss'] = isset($_ENV['XSS']) ? 
                ($_ENV['XSS'] === 'true') : false;
            
            // CSRF
            $this->config['csrf'] = isset($_ENV['CSRF']) ? 
                ($_ENV['CSRF'] === 'true') : false;
        }

        public function isEnabled($vulnerability) {
            return isset($this->config[$vulnerability]) && $this->config[$vulnerability];
        }

        public function getAllConfig() {
            return $this->config;
        }
    }

    // Helper function to check if a vulnerability is enabled
    function isVulnerabilityEnabled($vulnerability) {
        return VulnerabilityConfig::getInstance()->isEnabled($vulnerability);
    }
}
?> 