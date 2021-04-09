<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\Psr0Fixer;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
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
use Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;

// Suppress `Notice:`s in ECS 8.x This is probably fixed in the 9.x versions,
// but we can't update to that version, because it's PHP > 7.3 only.
// See: https://github.com/bolt/core/issues/2519
error_reporting(error_reporting() & ~E_NOTICE);

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('sets', ['clean-code', 'common', 'php70', 'php71', 'psr12', 'symfony', 'symfony-risky']);

    $parameters->set('paths', [
        __DIR__ . '/src',
        __DIR__ . '/ecs.php',
    ]);

    $parameters->set('cache_directory', 'var/cache/ecs');

    $parameters->set('skip', [
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
    ]);

    $services = $containerConfigurator->services();

    $services->set(StandaloneLineInMultilineArrayFixer::class);

    $services->set(BlankLineAfterStrictTypesFixer::class);

    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [['spacing' => 'one']]);

    $services->set(RemoveSuperfluousDocBlockWhitespaceFixer::class);

    $services->set(PhpUnitMethodCasingFixer::class);

    $services->set(FinalInternalClassFixer::class);

    $services->set(MbStrFunctionsFixer::class);

    $services->set(Psr0Fixer::class);

    $services->set(Psr4Fixer::class);

    $services->set(LowercaseCastFixer::class);

    $services->set(ShortScalarCastFixer::class);

    $services->set(BlankLineAfterOpeningTagFixer::class);

    $services->set(NoLeadingImportSlashFixer::class);

    $services->set(OrderedImportsFixer::class)
        ->call('configure', [[
            'importsOrder' => ['class', 'const', 'function'],
        ]]);

    $services->set(DeclareEqualNormalizeFixer::class)
        ->call('configure', [['space' => 'none']]);

    $services->set(NewWithBracesFixer::class);

    $services->set(BracesFixer::class)
        ->call('configure', [[
            'allow_single_line_closure' => false,
            'position_after_functions_and_oop_constructs' => 'next',
            'position_after_control_structures' => 'same',
            'position_after_anonymous_constructs' => 'same',
        ]]);

    $services->set(NoBlankLinesAfterClassOpeningFixer::class);

    $services->set(VisibilityRequiredFixer::class)
        ->call('configure', [[
            'elements' => ['const', 'method', 'property'],
        ]]);

    $services->set(TernaryOperatorSpacesFixer::class);

    $services->set(ReturnTypeDeclarationFixer::class);

    $services->set(NoTrailingWhitespaceFixer::class);

    $services->set(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);

    $services->set(NoWhitespaceBeforeCommaInArrayFixer::class);

    $services->set(WhitespaceAfterCommaInArrayFixer::class);

    $services->set(PhpdocToReturnTypeFixer::class);

    $services->set(FullyQualifiedStrictTypesFixer::class);

    $services->set(NoSuperfluousPhpdocTagsFixer::class);

    $services->set(PhpdocLineSpanFixer::class)
        ->call('configure', [['property' => 'single']]);

    $services->set(DisallowYodaComparisonSniff::class);
};
