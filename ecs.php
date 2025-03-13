<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\PhpdocToReturnTypeFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAnnotationWithoutDotFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

// Suppress `Notice:`s in ECS 8.x This is probably fixed in the 9.x versions,
// but we can't update to that version, because it's PHP > 7.3 only.
// See: https://github.com/bolt/core/issues/2519
error_reporting(error_reporting() & ~E_NOTICE);

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/ecs.php',
    ])
    ->withCache('var/cache/ecs')
    ->withPreparedSets(psr12: true, common: true, cleanCode: true)
    ->withSkip([
        OrderedClassElementsFixer::class => null,
        YodaStyleFixer::class => null,
        IncrementStyleFixer::class => null,
        PhpdocAnnotationWithoutDotFixer::class => null,
        PhpdocSummaryFixer::class => null,
        PhpdocAlignFixer::class => null,
        NativeConstantInvocationFixer::class => null,
        NativeFunctionInvocationFixer::class => null,
        UnaryOperatorSpacesFixer::class => null,
        ArrayOpenerAndCloserNewlineFixer::class => null,
        ArrayListItemNewlineFixer::class => null,
    ])
    ->withRules([
        StandaloneLineInMultilineArrayFixer::class,
        BlankLineAfterStrictTypesFixer::class,
        RemoveUselessDefaultCommentFixer::class,
        PhpUnitMethodCasingFixer::class,
        FinalInternalClassFixer::class,
        MbStrFunctionsFixer::class,
        LowercaseCastFixer::class,
        ShortScalarCastFixer::class,
        BlankLineAfterOpeningTagFixer::class,
        NoLeadingImportSlashFixer::class,
        NewWithBracesFixer::class,
        NoBlankLinesAfterClassOpeningFixer::class,
        TernaryOperatorSpacesFixer::class,
        ReturnTypeDeclarationFixer::class,
        NoTrailingWhitespaceFixer::class,
        NoSinglelineWhitespaceBeforeSemicolonsFixer::class,
        NoWhitespaceBeforeCommaInArrayFixer::class,
        WhitespaceAfterCommaInArrayFixer::class,
        PhpdocToReturnTypeFixer::class,
        FullyQualifiedStrictTypesFixer::class,
        DisallowYodaComparisonSniff::class,
    ])
    ->withConfiguredRule(NoSuperfluousPhpdocTagsFixer::class, ['remove_inheritdoc' => false])
    ->withConfiguredRule(ConcatSpaceFixer::class,
        ['spacing' => 'one'])
    ->withConfiguredRule(OrderedImportsFixer::class,
        [
            'imports_order' => ['class', 'const', 'function'],
        ])
    ->withConfiguredRule(DeclareEqualNormalizeFixer::class,
        ['space' => 'none'])
    ->withConfiguredRule(BracesFixer::class,

        [
            'allow_single_line_closure' => false,
            'position_after_functions_and_oop_constructs' => 'next',
            'position_after_control_structures' => 'same',
            'position_after_anonymous_constructs' => 'same',
        ]
    )
    ->withConfiguredRule(VisibilityRequiredFixer::class,
        [
            'elements' => ['const', 'method', 'property'],
        ])
    ->withConfiguredRule(PhpdocLineSpanFixer::class,
        ['property' => 'single']);
