<?php
declare(strict_types=1);

namespace DependencyAnalyzer\DependencyDumper;

use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName;
use DependencyAnalyzer\Exceptions\AnalyzedFileException;
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

    public function notifyAnalyzeFileError(AnalyzedFileException $e): void
    {
        return;
    }

    public function notifyResolveDependencyError(string $file, ResolveDependencyException $e): void
    {
        return;
    }

    public function notifyResolvePhpDocError(string $file, string $fqsen, InvalidFullyQualifiedStructureElementNameException $e): void
    {
        return;
    }
}
