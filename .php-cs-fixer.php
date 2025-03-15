<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$rules = [
    '@Symfony' => true,
    '@PHP74Migration' => true,
    '@PHP74Migration:risky' => true,
    'phpdoc_to_property_type' => true,
    // 'phpdoc_to_param_type' => true, // requires PHP 8.0
    'phpdoc_to_return_type' => true,
    // overwrite some Symfony rules
    'concat_space' => ['spacing' => 'one'],
    'phpdoc_align' => false,
    'yoda_style' => false,
];

$config = new PhpCsFixer\Config();

return $config
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setFinder($finder);
