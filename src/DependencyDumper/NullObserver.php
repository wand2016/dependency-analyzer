<?php
declare(strict_types=1);

namespace DependencyAnalyzer\DependencyDumper;

use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName;
use DependencyAnalyzer\Exceptions\AnalysedFileException;
use DependencyAnalyzer\Exceptions\InvalidFullyQualifiedStructureElementNameException;
use DependencyAnalyzer\Exceptions\ResolveDependencyException;

class NullObserver implements ObserverInterface
{
    public function start(int $max): void
    {
        return;
    }

    public function end(): void
    {
        return;
    }

    public function update(string $currentFile): void
    {
        return;
    }

    public function notifyAnalyzeFileError(AnalysedFileException $e): void
    {
        return;
    }

    public function notifyResolveDependencyError(string $file, ResolveDependencyException $e): void
    {
        return;
    }

    public function notifyResolvePhpDocError(string $file, FullyQualifiedStructuralElementName $fqsen, InvalidFullyQualifiedStructureElementNameException $e): void
    {
        return;
    }
}