{
    "name": "laminas/laminas-feed",
    "description": "provides functionality for consuming RSS and Atom feeds",
    "license": "BSD-3-Clause",
    "keywords": [
        "laminas",
        "feed"
    ],
    "homepage": "https://laminas.dev",
    "support": {
        "docs": "https://docs.laminas.dev/laminas-feed/",
        "issues": "https://github.com/laminas/laminas-feed/issues",
        "source": "https://github.com/laminas/laminas-feed",
        "rss": "https://github.com/laminas/laminas-feed/releases.atom",
        "chat": "https://laminas.dev/chat",
        "forum": "https://discourse.laminas.dev"
    },
    "config": {
        "sort-packages": true
    },
    "extra": {
        "branch-alias": {
            "dev-master": "2.12.x-dev",
            "dev-develop": "2.13.x-dev"
        }
    },
    "require": {
        "php": "^5.6 || ^7.0",
        "ext-dom": "*",
        "ext-libxml": "*",
        "laminas/laminas-escaper": "^2.5.2",
        "laminas/laminas-stdlib": "^3.2.1",
        "laminas/laminas-zendframework-bridge": "^1.0"
    },
    "require-dev": {
        "laminas/laminas-cache": "^2.7.2",
        "laminas/laminas-coding-standard": "~1.0.0",
        "laminas/laminas-db": "^2.8.2",
        "laminas/laminas-http": "^2.7",
        "laminas/laminas-servicemanager": "^2.7.8 || ^3.3",
        "laminas/laminas-validator": "^2.10.1",
        "phpunit/phpunit": "^5.7.27 || ^6.5.14 || ^7.5.20",
        "psr/http-message": "^1.0.1"
    },
    "suggest": {
        "laminas/laminas-cache": "Laminas\\Cache component, for optionally caching feeds between requests",
        "laminas/laminas-db": "Laminas\\Db component, for use with PubSubHubbub",
        "laminas/laminas-http": "Laminas\\Http for PubSubHubbub, and optionally for use with Laminas\\Feed\\Reader",
        "laminas/laminas-servicemanager": "Laminas\\ServiceManager component, for easily extending ExtensionManager implementations",
        "laminas/laminas-validator": "Laminas\\Validator component, for validating email addresses used in Atom feeds and entries when using the Writer subcomponent",
        "psr/http-message": "PSR-7 ^1.0.1, if you wish to use Laminas\\Feed\\Reader\\Http\\Psr7ResponseDecorator"
    },
    "autoload": {
        "psr-4": {
            "Laminas\\Feed\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "LaminasTest\\Feed\\": "test/"
        }
    },
    "scripts": {
        "check": [
            "@cs-check",
            "@test"
        ],
        "cs-check": "phpcs",
        "cs-fix": "phpcbf",
        "test": "phpunit --colors=always",
        "test-coverage": "phpunit --colors=always --coverage-clover clover.xml"
    },
    "replace": {
        "zendframework/zend-feed": "^2.12.0"
    }
}
