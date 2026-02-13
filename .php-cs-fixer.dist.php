<?php

$finder = (new PhpCsFixer\Finder())
    ->in('scripts')
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'phpdoc_annotation_without_dot' => false,
        'nullable_type_declaration_for_default_null_value' => [
            'use_nullable_type_declaration' => true,
        ],
        'phpdoc_to_comment' => [
            'ignored_tags' => ['psalm-suppress', 'phpstan-ignore-line', 'phpstan-ignore-next-line'],
        ],
    ])
    ->setFinder($finder)
;
