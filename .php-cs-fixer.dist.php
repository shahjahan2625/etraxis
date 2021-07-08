<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->notPath(['Kernel.php', 'bootstrap.php'])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([

        //--------------------------------------------------------------
        //  Rule sets
        //--------------------------------------------------------------
        '@Symfony'                   => true,
        '@Symfony:risky'             => true,
        '@PhpCsFixer'                => true,
        '@PhpCsFixer:risky'          => true,
        '@DoctrineAnnotation'        => true,
        '@PHP74Migration'            => true,
        '@PHP74Migration:risky'      => true,
        '@PHPUnit84Migration:risky'  => true,

        //--------------------------------------------------------------
        //  Rules override
        //--------------------------------------------------------------
        'binary_operator_spaces'     => ['default' => 'align'],
        'braces'                     => false,
        'declare_strict_types'       => false,
        'native_function_invocation' => false,
        'self_static_accessor'       => true,
    ])
    ->setFinder($finder)
;
