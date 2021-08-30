<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Integration',
    'description' => 'Matomo integration for TYPO3',
    'category' => 'fe',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.15-11.5.99',
            'php' => '7.4.0-0.0.0',
        ],
        'conflicts' => [],
        'suggests' => [
            'matomo_optout' => '',
            'matomo_widgets' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\MatomoIntegration\\' => 'Classes']
    ],
];
