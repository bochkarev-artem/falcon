<?php

$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
                   '@PHP71Migration'                       => true,
                   'psr0'                                  => true,
                   'psr4'                                  => true,
                   '@PSR1'                                 => true,
                   '@PSR2'                                 => true,
                   'declare_equal_normalize'               => ["space" => 'none'],
                   'align_multiline_comment'               => true,
                   'array_syntax'                          => ['syntax' => 'short'],
                   'cast_spaces'                           => ['space' => 'none'],
                   'ordered_class_elements'                => true,
                   'ordered_imports'                       => true,
                   'doctrine_annotation_braces'            => true,
                   'doctrine_annotation_indentation'       => true,
                   'doctrine_annotation_spaces'            => true,
                   'blank_line_before_statement'           => true,
                   'combine_consecutive_unsets'            => true,
                   'no_mixed_echo_print'                   => true,
                   'whitespace_after_comma_in_array'       => true,
                   'self_accessor'                         => true,
                   'single_quote'                          => true,
                   'standardize_not_equals'                => true,
                   'strict_param'                          => true,
                   'short_scalar_cast'                     => true,
                   'return_type_declaration'               => ["space_before" => "none"],
                   'normalize_index_brace'                 => true,
                   'trailing_comma_in_multiline_array'     => true,
                   'trim_array_spaces'                     => true,
                   'visibility_required'                   => true,
                   'list_syntax'                           => ['syntax' => 'short'],
                   'method_argument_space'                 => ['ensure_fully_multiline' => true],
                   'no_extra_consecutive_blank_lines'      => [
                       'tokens' => [
                           'break',
                           'continue',
                           'extra',
                           'return',
                           'throw',
                           'use',
                           'parenthesis_brace_block',
                           'square_brace_block',
                           'curly_brace_block',
                       ],
                   ],
                   'no_unreachable_default_argument_value' => true,
                   'no_useless_else'                       => true,
                   'no_useless_return'                     => true,
                   'php_unit_strict'                       => false,
                   'phpdoc_add_missing_param_annotation'   => true,
                   'phpdoc_order'                          => true,
                   'phpdoc_types_order'                    => true,
                   'phpdoc_scalar'                         => true,
                   'phpdoc_trim'                           => true,
                   'phpdoc_types'                          => true,
                   'phpdoc_var_without_name'               => true,
                   'semicolon_after_instruction'           => true,
                   'single_line_comment_style'             => true,
                   'ternary_operator_spaces'               => true,
                   'lowercase_cast'                        => true,
                   'binary_operator_spaces'                => [
                       "align_double_arrow" => true,
                       "align_equals"       => true,
                   ],
               ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in("./src")
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
    );

PhpCsFixer\FixerFactory::create()
    ->registerBuiltInFixers()
    ->registerCustomFixers($config->getCustomFixers())
    ->useRuleSet(new PhpCsFixer\RuleSet($config->getRules()));

return $config;
