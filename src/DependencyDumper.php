<?php
declare(strict_types=1);

namespace DependencyAnalyzer;

use DependencyAnalyzer\DependencyDumper\CollectDependenciesVisitor;
use DependencyAnalyzer\DependencyDumper\NullObserver;
use DependencyAnalyzer\DependencyDumper\ObserverInterface;
use DependencyAnalyzer\Exceptions\AnalyzedFileException;
use PHPStan\AnalysedCodeException;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Parser\Parser;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\File\FileFinder;
use PHPStan\ShouldNotHappenException;

class DependencyDumper
{
    /**
     * @var CollectDependenciesVisitor
     */
    protected $collectNodeVisitor;

    /**
     * @var NodeScopeResolver
     */
    protected $nodeScopeResolver;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ScopeFactory
     */
    protected $scopeFactory;

    /**
     * @var FileFinder
     */
    protected $fileFinder;

    /**
     * @var ObserverInterface
     */
    protected static $observer = null;

    public function __construct(
        NodeScopeResolver $nodeScopeResolver,
        Parser $parser,
        ScopeFactory $scopeFactory,
        FileFinder $fileFinder,
        CollectDependenciesVisitor $collectNodeVisitor
    )
    {
        $this->nodeScopeResolver = $nodeScopeResolver;
        $this->parser = $parser;
        $this->scopeFactory = $scopeFactory;
        $this->fileFinder = $fileFinder;
        $this->collectNodeVisitor = $collectNodeVisitor;
    }

    public static function createFromConfig(string $currentDir, string $tmpDir, array $additionalConfigFiles): self
    {
        $phpStanContainer = (new ContainerFactory($currentDir))->create($tmpDir, $additionalConfigFiles, []);

        return new self(
            $phpStanContainer->getByType(NodeScopeResolver::class),
            $phpStanContainer->getByType(Parser::class),
            $phpStanContainer->getByType(ScopeFactory::class),
            $phpStanContainer->getByType(FileFinder::class),
            $phpStanContainer->getByType(CollectDependenciesVisitor::class)
        );
    }

    public function dump(array $paths, array $excludePaths = []): DependencyGraph
    {
        $excludeFiles = $this->getAllFilesRecursive($excludePaths);
        $files = $this->getAllFilesRecursive($paths);

        $this->notifyDumpStart(array_reduce($files, function (int $max, string $file) use ($excludeFiles) {
            if (!in_array($file, $excludeFiles)) {
                $max++;
            }

            return $max;
        }, 0));
        foreach ($files as $file) {
            if (!in_array($file, $excludeFiles)) {
                $this->notifyCurrentFile($file);

                try {
                    $this->dumpFile($file);
                } catch (AnalyzedFileException $e) {
                    $this->notifyAnalyzedFileException($e);
                }
            }
        }
        $this->notifyDumpEnd();

        return $this->collectNodeVisitor->getDependencyGraphBuilder()->build();
    }

    protected function dumpFile(string $file): void
    {
        try {
            $this->collectNodeVisitor->setFile($file);

            // collect dependencies in $this->collectNodeVisitor
            $this->nodeScopeResolver->processNodes(
                $this->parser->parseFile($file),
                $this->scopeFactory->create(ScopeContext::create($file)),
                \Closure::fromCallable($this->collectNodeVisitor)  // type hint of processNodes() is \Closure...
            );
        } catch (ShouldNotHappenException $e) {
            throw new AnalyzedFileException($file, 'analyzing file is failed, because unexpected error', 0, $e);
        } catch (AnalysedCodeException $e) {
            throw new AnalyzedFileException($file, 'analyzing file is failed, because unexpected error', 0, $e);
        }
    }

    protected function getAllFilesRecursive(array $paths): array
    {
        try {
            $fileFinderResult = $this->fileFinder->findFiles($paths);
        } catch (\PHPStan\File\PathNotFoundException $e) {
            throw new AnalyzedFileException($e->getPath(), 'path was not found.', 0, $e);
        }

        return $fileFinderResult->getFiles();
    }

    public function setObserver(ObserverInterface $observer = null): self
    {
        self::$observer = $observer;
        $this->collectNodeVisitor->getDependencyGraphBuilder()->setObserver($observer);

        return $this;
    }

    /**
     * @return ObserverInterface
     * @da-internal \DependencyAnalyzer\DependencyDumper
     * @da-internal \DependencyAnalyzer\DependencyDumper\
     */
    public static function getObserver(): ObserverInterface
    {
        if (!is_null(self::$observer)) {
            return self::$observer;
        }

        return new NullObserver();
    }

    protected function notifyDumpStart(int $max): void
    {
        self::getObserver()->start($max);
    }

    protected function notifyCurrentFile(string $file): void
    {
        self::getObserver()->update($file);
    }

    protected function notifyAnalyzedFileException(AnalyzedFileException $e): void
    {
        self::getObserver()->notifyAnalyzeFileError($e);
    }

    protected function notifyDumpEnd(): void
    {
        self::getObserver()->end();
    }
}
