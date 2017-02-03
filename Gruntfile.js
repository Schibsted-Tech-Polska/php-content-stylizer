'use strict';

module.exports = function (grunt) {
    grunt.initConfig({
        phpcpd: {
            application: {
                dir: 'src'
            },
            options: {
                bin: 'vendor/bin/phpcpd'
            }
        },
        phpcs: {
            options: {
                bin: 'vendor/bin/phpcs',
                standard: 'PSR2'
            },
            application: {
                src: [
                  'src/**/*.php'
                ]
            }
        },
        phpmd: {
            application: {
                dir: 'src'
            },
            options: {
                bin: 'vendor/bin/phpmd',
                reportFormat: 'text'
            }
        },
        phpunit: {
            default: {
                dir: 'src'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                colors: true,
                configuration: 'phpunit.xml'
            }
        }
    });

    grunt.loadNpmTasks('grunt-phpcpd');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpmd');
    grunt.loadNpmTasks('grunt-phpunit');

    grunt.registerTask('test', [
        'phpcpd',
        'phpcs',
        'phpmd',
        'phpunit'
    ]);
};
