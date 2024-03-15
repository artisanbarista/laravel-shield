<?php

/*
 * You can place your custom package configuration in here.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Enables Laravel Shield
    |--------------------------------------------------------------------------
    |
    | Determines whether middleware checks for malicious URLs.
    | Can be set by SHIELD_PROTECTION_ENABLED in .env file.
    |
    */
    'protection_enabled' => env('SHIELD_PROTECTION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Enables Logging
    |--------------------------------------------------------------------------
    |
    | Logs for when malicious request is detected/blocked.
    | Can be set by SHIELD_LOGGING_ENABLED in .env file.
    |
    */
    'logging_enabled' => env('SHIELD_LOGGING_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Expiration time
    |--------------------------------------------------------------------------
    |
    | Number of seconds before a blocked ip is deleted from the blocked_ips table.
    | Can be set by SHIELD_EXPIRATION_TIME in .env file.
    |
    */
    'expiration_time' => env('SHIELD_EXPIRATION_TIME', 10800),

    /*
    |--------------------------------------------------------------------------
    | Max Attempts
    |--------------------------------------------------------------------------
    |
    | Number of attempts allowed before blocking the IP.
    | Can be set by SHIELD_MAX_ATTEMPTS in .env file.
    |
    */
    'max_attempts' => env('SHIELD_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Malicious URLS
    |--------------------------------------------------------------------------
    |
    |   These are common paths that attackers might try to access to exploit vulnerabilities or gather information.
    | - A malicious URL parts needs only be part of the URL. Example:
    |   'wp-admin' triggers the URL '/wp-admin', but also 'wp-admin/foo' and 'index.php?wp-admin=bar'
    | - the URL strings are case insensitive.
    |
    */
    'malicious_urls' => [
        'call_user_func_array', 'invokefunction', 'wp-admin', 'wp-login', '.git', '.env', 'install.php', '/vendor',
        'swagger-ui.html', 'api-doc', 'cgi-bin', 'asdlp', 'web-inf', '../', '.jsp', '/admin', 'wp-includes', 'regex'
    ],

    /*
    |--------------------------------------------------------------------------
    | Malicious Patterns
    |--------------------------------------------------------------------------
    |
    |   These are regular expressions that match patterns commonly found in attacks:
    |
    |   File Inclusion:
    |   Detects PHP wrappers which can be used in file inclusion attacks.
    |
    |   LFI: This pattern detects Local File Inclusion.
    |   Attacks where an attacker attempts to access files on the server by using relative paths.
    |
    |   RFI: This pattern detects Remote File Inclusion.
    |   Attacks where an attacker attempts to make the server execute or display content from a remote server.
    |
    |   SQLi: These patterns detect SQL Injection
    |   Attacks where an attacker attempts to manipulate SQL queries by injecting malicious SQL code.
    |
    |   XSS: This pattern is used to detect Cross-Site Scripting (XSS) attacks.
    |
    |   Command Injection:
    |   Occur when an attacker is able to execute arbitrary commands on the host operating system.
    |   This pattern matches any string that contains characters often used in command injection attacks,
    |   such as semicolon (;), pipe (|), ampersand (&), newline (\n), carriage return (\r), dollar sign ($),
    |   parentheses ((or)), backtick (``  ``), double quote ("), single quote ('), curly braces ({ or }),
    |   square brackets ([ or ]), less than (<), greater than (>), or tilde slash (~/).
    |
    |   Path Traversal:
    |   This pattern is used to detect Directory Traversal attacks, also known as Path Traversal.
    |   These attacks exploit insufficient security validation/sanitization of user-supplied input file names.
    |   The pattern matches any string that contains ../, ..\, or ~/, which are used in directory traversal attacks to
    |   navigate file directories.
    |
    */
    'malicious_patterns' => [
        // PHP wrappers.
        '#php://#','#glob://#', '#phar://#', '#bzip2://#', '#expect://#', '#ogg://#', '#rar://#', '#ssh2://#',
        '#zip://#', '#zlib://#',

        // LFI.
        '#\.\/#is',

        // RFI.
        //'#(http|ftp){1,1}(s){0,1}://.*#i',

        // SQLi
        '#[\d\W](union select|union join|union distinct)[\d\W]#is',
        '#[\d\W](union|union select|insert|from|where|concat|into|cast|truncate|select|delete|having)[\d\W]#is',

        // XSS.
        '/<script[^>]*?>.*?<\/script>/is',

        // Command Injection.
        '/;|\||&|\n|\r|\$|\(|\)|\`|\"|\'|\{|\}|\[|\]|<|>|~/is',

        // Path Traversal.
        '/(\%255c|\%252e){2,}/is',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie Malicious Patterns
    |--------------------------------------------------------------------------
    |
    |   These are regular expressions that match patterns commonly found in attacks:
    |   CRLF:
    |   This pattern is used to detect Carriage Return Line Feed (CRLF) Injection attacks. CRLF Injection attacks occur
    |   when an attacker inserts a CRLF sequence into an HTTP stream, which can lead to response splitting and other
    |   results. The pattern matches any string that contains %0d, %0a, \r, or \n, which represent CRLF characters.
    |
    */
    'malicious_cookie_patterns' => [
        '/\%0d|\%0a|\r|\n/is', // CRLF Injection
    ],

    /*
    |--------------------------------------------------------------------------
    | Malicious User Agents
    |--------------------------------------------------------------------------
    |
    | These are user agents that are known to be used by malicious bots.
    | A malicious User Agents string needs only be part of the User Agents.
    | The User Agent strings are case-insensitive.
    |
    | Example:
    |   'dotbot' triggers the User Agent
    |   'Mozilla/5.0 (compatible; DotBot/1.1; http://www.dotnetdotcom.org/, crawler@dotnetdotcom.org)'
    |
    */
    'malicious_user_agents' => [
        'dotbot', 'linguee', 'sqlmap', 'nikto', 'python', 'perl', 'nmap', 'winhttp', 'clshttp', 'loader'
    ],

    'whitelist_hosts' => [
        '.google.com', '.googlebot.com', '.googleusercontent.com'
    ],

    /*
    |--------------------------------------------------------------------------
    | Block IPs Store
    |--------------------------------------------------------------------------
    |
    | The implementation you use to store blocked IPs with.
    |
    | Can be set by SHIELD_STORAGE_IMPLEMENTATION_CLASS in.env file
    |
    */
    'storage_implementation_class' =>
        env('SHIELD_STORAGE_IMPLEMENTATION_CLASS', '\Webdevartisan\LaravelShield\Services\BlockedIpStoreRateLimiter'),
];
