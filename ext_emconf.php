<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Integration',
    'description' => 'Matomo integration for TYPO3',
    'category' => 'fe',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '1.6.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.15-12.4.99',
            'php' => '7.4.0-0.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\MatomoIntegration\\' => 'Classes']
    ],
];
