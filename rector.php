<?php

declare(strict_types = 1);

use Rector\CodeQuality\Rector\ClassMethod\DateTimeToDateTimeInterfaceRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\Assign\PHPStormVarAnnotationRector;
use Rector\CodingStyle\Rector\ClassMethod\ReturnArrayClassMethodToYieldRector;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\ClassMethod\YieldClassMethodToArrayClassMethodRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\CodingStyle\Rector\FuncCall\FunctionCallToConstantRector;
use Rector\CodingStyle\Rector\Function_\CamelCaseFunctionNamingToUnderscoreRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\CodingStyle\Rector\Property\AddFalseDefaultToBoolPropertyRector;
use Rector\CodingStyle\Rector\String_\UseClassKeywordForClassNameResolutionRector;
use Rector\CodingStyle\Rector\Use_\RemoveUnusedAliasRector;
use Rector\Core\Configuration\Option;
use Rector\DeadCode\Rector\Class_\RemoveUnusedClassesRector;
use Rector\DeadCode\Rector\Class_\RemoveUnusedDoctrineEntityMethodAndPropertyRector;
use Rector\DeadCode\Rector\Class_\RemoveUselessJustForSakeInterfaceRector;
use Rector\DeadCode\Rector\ClassConst\RemoveUnusedClassConstantRector;
use Rector\DeadCode\Rector\ClassConst\RemoveUnusedPrivateConstantRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDeadRecursiveClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDelegatingParentCallRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodRector;
use Rector\DeadCode\Rector\Function_\RemoveUnusedFunctionRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveOverriddenValuesRector;
use Rector\DeadCode\Rector\Property\RemoveSetterOnlyPropertyAndMethodCallRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector;
use Rector\DeadCode\Rector\TryCatch\RemoveDeadTryCatchRector;
use Rector\DoctrineCodeQuality\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\DoctrineCodeQuality\Rector\Class_\MoveRepositoryFromParentToConstructorRector;
use Rector\DoctrineCodeQuality\Rector\MethodCall\ChangeSetParametersArrayToArrayCollectionRector;
use Rector\DoctrineCodeQuality\Rector\Property\CorrectDefaultTypesOnEntityPropertyRector;
use Rector\DoctrineCodeQuality\Rector\Property\ImproveDoctrineCollectionDocTypeInEntityRector;
use Rector\Php70\Rector\Ternary\TernaryToNullCoalescingRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void
{
    // get parameters
    $parameters = $containerConfigurator->parameters();

    $parameters->set(
        Option::SKIP,
        [
            IssetOnPropertyObjectToPropertyExistsRector::class,
            TernaryToNullCoalescingRector::class,
            NullableCompareToNullRector::class,
            DateTimeToDateTimeInterfaceRector::class,
            ExplicitBoolCompareRector::class,
            AddFalseDefaultToBoolPropertyRector::class,
            CamelCaseFunctionNamingToUnderscoreRector::class,
            ConsistentPregDelimiterRector::class,
            FunctionCallToConstantRector::class,
            PostIncDecToPreIncDecRector::class,
            PreferThisOrSelfMethodCallRector::class,
            RemoveUnusedAliasRector::class,
            ReturnArrayClassMethodToYieldRector::class,
            UnSpreadOperatorRector::class,
            UseClassKeywordForClassNameResolutionRector::class,
            YieldClassMethodToArrayClassMethodRector::class,
            RemoveDeadRecursiveClassMethodRector::class,
            RemoveDeadTryCatchRector::class,
            RemoveDelegatingParentCallRector::class,
            RemoveNullPropertyInitializationRector::class,
            RemoveOverriddenValuesRector::class,
            RemoveSetterOnlyPropertyAndMethodCallRector::class,
            RemoveUnusedClassConstantRector::class,
            RemoveUnusedClassesRector::class,
            RemoveUnusedDoctrineEntityMethodAndPropertyRector::class,
            RemoveUnusedFunctionRector::class,
            RemoveUnusedPrivateConstantRector::class,
            RemoveUnusedPrivateMethodRector::class,
            RemoveUnusedPrivatePropertyRector::class,
            RemoveUnusedPublicMethodRector::class,
            RemoveUselessJustForSakeInterfaceRector::class,
            ChangeSetParametersArrayToArrayCollectionRector::class,
            MoveCurrentDateTimeDefaultInEntityToConstructorRector::class,
            MoveRepositoryFromParentToConstructorRector::class,
            CorrectDefaultTypesOnEntityPropertyRector::class,
            ImproveDoctrineCollectionDocTypeInEntityRector::class,
        ]
    );

//     Define what rule sets will be applied
    $parameters->set(
        Option::SETS,
        [
            SetList::CODE_QUALITY,
            SetList::CODE_QUALITY_STRICT,
            SetList::TYPE_DECLARATION,
            SetList::CODING_STYLE,
            SetList::DEAD_CODE,
            SetList::DOCTRINE_CODE_QUALITY,

        ]
    );

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
