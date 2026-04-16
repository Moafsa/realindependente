<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | application. These settings help protect against common security
    | vulnerabilities and ensure proper data handling.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Password Requirements
    |--------------------------------------------------------------------------
    |
    | These options control the password requirements for user accounts.
    |
    */

    'password' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', true),
        'max_age_days' => env('PASSWORD_MAX_AGE_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | These options control session security settings.
    |
    */

    'session' => [
        'lifetime' => env('SESSION_LIFETIME', 120),
        'encrypt' => env('SESSION_ENCRYPT', true),
        'secure' => env('SESSION_SECURE', false),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | These options control rate limiting for various endpoints.
    |
    */

    'rate_limiting' => [
        'login_attempts' => env('RATE_LIMIT_LOGIN_ATTEMPTS', 5),
        'login_decay_minutes' => env('RATE_LIMIT_LOGIN_DECAY', 15),
        'api_requests' => env('RATE_LIMIT_API_REQUESTS', 60),
        'api_decay_minutes' => env('RATE_LIMIT_API_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    |
    | These options control file upload security settings.
    |
    */

    'file_upload' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10485760), // 10MB
        'allowed_types' => explode(',', env('UPLOAD_ALLOWED_TYPES', 'image/jpeg,image/png,image/gif,image/webp')),
        'scan_for_viruses' => env('UPLOAD_SCAN_VIRUSES', false),
        'quarantine_suspicious' => env('UPLOAD_QUARANTINE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    |
    | These options control API security settings.
    |
    */

    'api' => [
        'require_https' => env('API_REQUIRE_HTTPS', true),
        'cors_origins' => explode(',', env('API_CORS_ORIGINS', '')),
        'cors_methods' => explode(',', env('API_CORS_METHODS', 'GET,POST,PUT,DELETE,OPTIONS')),
        'cors_headers' => explode(',', env('API_CORS_HEADERS', 'Content-Type,Authorization,X-Requested-With')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Security
    |--------------------------------------------------------------------------
    |
    | These options control database security settings.
    |
    */

    'database' => [
        'encrypt_connections' => env('DB_ENCRYPT_CONNECTIONS', true),
        'ssl_mode' => env('DB_SSL_MODE', 'require'),
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 30),
        'query_timeout' => env('DB_QUERY_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Security
    |--------------------------------------------------------------------------
    |
    | These options control security-related logging.
    |
    */

    'logging' => [
        'log_failed_logins' => env('LOG_FAILED_LOGINS', true),
        'log_suspicious_activity' => env('LOG_SUSPICIOUS_ACTIVITY', true),
        'log_data_access' => env('LOG_DATA_ACCESS', false),
        'log_admin_actions' => env('LOG_ADMIN_ACTIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | These options control Content Security Policy headers.
    |
    */

    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
        'default_src' => env('CSP_DEFAULT_SRC', "'self'"),
        'script_src' => env('CSP_SCRIPT_SRC', "'self' 'unsafe-inline'"),
        'style_src' => env('CSP_STYLE_SRC', "'self' 'unsafe-inline'"),
        'img_src' => env('CSP_IMG_SRC', "'self' data: https:"),
        'font_src' => env('CSP_FONT_SRC', "'self' https:"),
        'connect_src' => env('CSP_CONNECT_SRC', "'self'"),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | These options control security headers.
    |
    */

    'headers' => [
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),
        'x_frame_options' => env('X_FRAME_OPTIONS', 'DENY'),
        'x_content_type_options' => env('X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'x_xss_protection' => env('X_XSS_PROTECTION', '1; mode=block'),
        'strict_transport_security' => env('STRICT_TRANSPORT_SECURITY', 'max-age=31536000; includeSubDomains'),
        'referrer_policy' => env('REFERRER_POLICY', 'strict-origin-when-cross-origin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    |
    | These options control data encryption settings.
    |
    */

    'encryption' => [
        'enabled' => true,
        'algorithm' => 'AES-256-CBC',
        'encrypt_medical_data' => env('ENCRYPT_MEDICAL_DATA', true),
        'encrypt_financial_data' => env('ENCRYPT_FINANCIAL_DATA', true),
        'encrypt_personal_data' => env('ENCRYPT_PERSONAL_DATA', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Brute Force Protection
    |--------------------------------------------------------------------------
    |
    | These options control brute force attack protection.
    |
    */

    'brute_force' => [
        'enabled' => env('BRUTE_FORCE_PROTECTION', true),
        'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('LOCKOUT_DURATION', 900), // 15 minutes in seconds
        'track_by_ip' => true,
        'track_by_email' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Management
    |--------------------------------------------------------------------------
    |
    | These options control API token management.
    |
    */

    'tokens' => [
        'expiration' => env('TOKEN_EXPIRATION', 60), // minutes
        'refresh_token_expiration' => env('REFRESH_TOKEN_EXPIRATION', 10080), // 7 days in minutes
        'allow_multiple_sessions' => env('ALLOW_MULTIPLE_SESSIONS', true),
        'revoke_on_password_change' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Protection & Compliance
    |--------------------------------------------------------------------------
    |
    | These options control GDPR/LGPD compliance settings.
    |
    */

    'data_protection' => [
        'gdpr_compliance' => env('GDPR_COMPLIANCE', true),
        'lgpd_compliance' => true,
        'data_retention_days' => env('DATA_RETENTION_DAYS', 365),
        'anonymize_deleted_data' => true,
        'allow_data_export' => true,
        'allow_data_deletion' => true,
        'require_consent' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Alerts
    |--------------------------------------------------------------------------
    |
    | These options control security monitoring and alerts.
    |
    */

    'monitoring' => [
        'enabled' => env('MONITORING_ENABLED', true),
        'alert_on_suspicious_activity' => true,
        'alert_on_failed_logins' => true,
        'alert_on_data_breach' => true,
        'alert_email' => env('ERROR_REPORTING_EMAIL'),
        'alert_threshold' => 10, // Number of suspicious events before alerting
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Settings
    |--------------------------------------------------------------------------
    |
    | These options control backup security settings.
    |
    */

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'frequency' => env('BACKUP_FREQUENCY', 'daily'),
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
        'encrypt_backups' => true,
        'verify_integrity' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Management
    |--------------------------------------------------------------------------
    |
    | These options control IP whitelist/blacklist settings.
    |
    */

    'ip_management' => [
        'whitelist_enabled' => env('IP_WHITELIST_ENABLED', false),
        'whitelist' => explode(',', env('ALLOWED_IPS', '')),
        'blacklist_enabled' => env('IP_BLACKLIST_ENABLED', true),
        'blacklist' => explode(',', env('BLOCKED_IPS', '')),
        'auto_block_suspicious' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    |
    | These options control 2FA settings.
    |
    */

    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', false),
        'required_for_admin' => env('TWO_FACTOR_REQUIRED_ADMIN', false),
        'methods' => ['email', 'sms', 'authenticator'],
        'code_expiration' => 300, // 5 minutes in seconds
    ],
];
