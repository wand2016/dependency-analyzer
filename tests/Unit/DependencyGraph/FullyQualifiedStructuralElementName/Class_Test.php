<?php
declare(strict_types=1);

namespace Tests\Unit\DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName;

use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Base;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Class_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\ClassConstant;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Constant;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Function_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Interface_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Method;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Namespace_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Property;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Trait_;
use Tests\TestCase;

class Class_Test extends TestCase
{
    public function provideInclude()
    {
        return [
            'class1' => [new Class_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1'), true],
            'class2' => [new Class_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2'), false],
            'method1' => [new Method('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::someMethod()'), true],
            'method2' => [new Method('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2::someMethod()'), false],
            'property1' => [new Property('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::$someProperty'), true],
            'property2' => [new Property('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2::$someProperty'), false],
            'class constant1' => [new ClassConstant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::SOME_CONSTANT'), true],
            'class constant2' => [new ClassConstant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2::SOME_CONSTANT'), false],
            'namespace1' => [new Namespace_('\Tests\Fixtures\FullyQualifiedStructuralElementName\\'), false],
            'namespace2' => [new Namespace_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass\\'), false],
            'interface' => [new Interface_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeInterface1'), false],
            'trait' => [new Trait_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeTrait1'), false],
            'function' => [new Function_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeFunction()'), false],
            'constant' => [new Constant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SOME_CONSTANT'), false],
        ];
    }

    /**
     * @param Base $target
     * @param bool $expected
     * @dataProvider provideInclude
     */
    public function testInclude(Base $target, bool $expected)
    {
        $sut = new Class_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1');

        $this->assertSame($expected, $sut->include($target));
    }

    public function provideGetFullyQualifiedNamespaceName()
    {
        return [
            ['\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass', ['Tests', 'Fixtures', 'FullyQualifiedStructuralElementName']],
            ['\SomeClass', []]
        ];
    }

    /**
     * @param string $className
     * @param array $expected
     * @dataProvider provideGetFullyQualifiedNamespaceName
     */
    public function testGetFullyQualifiedNamespaceName(string $className, array $expected)
    {
        $sut = new Class_($className);

        $this->assertSame($expected, $sut->getFullyQualifiedNamespaceName());
    }

    public function provideGetFullyQualifiedClassName()
    {
        return [
            ['\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass', ['Tests', 'Fixtures', 'FullyQualifiedStructuralElementName', 'SomeClass']],
            ['\SomeClass', ['SomeClass']]
        ];
    }

    /**
     * @param string $className
     * @param array $expected
     * @dataProvider provideGetFullyQualifiedClassName
     */
    public function testGetFullyQualifiedClassName(string $className, array $expected)
    {
        $sut = new Class_($className);

        $this->assertSame($expected, $sut->getFullyQualifiedClassName());
    }
}
