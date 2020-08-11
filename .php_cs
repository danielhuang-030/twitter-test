<?php
return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],

        'phpdoc_no_empty_return' => false,
        'phpdoc_no_package' => false,
        'no_superfluous_phpdoc_tags' =>false,
    ]);
