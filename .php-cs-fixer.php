<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$rules = [
    '@Symfony' => true,
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
