<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_annotation_without_dot' => false,
        'nullable_type_declaration_for_default_null_value' => [
            'use_nullable_type_declaration' => true,
        ],
        'phpdoc_to_comment' => [
            'ignored_tags' => ['psalm-suppress'],
        ],
    ])
    ->setFinder($finder)
;
