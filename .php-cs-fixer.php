<?php

$header = <<<TXT
This file is part of the Apirone API.
 
(c) Alex Zaytseff <alex.zaytseff@gmail.com>
 
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
TXT;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor']);
                        

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        // '@PSR2' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'compact_nullable_typehint' => true,
        'logical_operators' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'return',
                'throw',
            ]
        ],
        'control_structure_continuation_position' => ['position' => 'next_line'],
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'both',
        ],
        // 'void_return' => false,
    ]
)
    ->setRiskyAllowed(true)
    ->setFinder($finder);
