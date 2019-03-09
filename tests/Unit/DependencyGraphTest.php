<?php
declare(strict_types=1);

namespace Tests\Unit\DependencyAnalyzer;

use DependencyAnalyzer\DependencyGraph;
use Fhaculty\Graph\Graph;
use Tests\TestCase;

class DependencyGraphTest extends TestCase
{
    public function testToArray()
    {
        $sut = new DependencyGraph($this->getSingleDependencyGraph());

        $this->assertEquals([
            'v1' => ['v2', 'v3'],
            'v2' => ['v3'],
            'v3' => [],
        ], $sut->toArray());
    }

    public function testCount()
    {
        $sut = new DependencyGraph($this->getSingleDependencyGraph());

        $this->assertCount(3, $sut);
    }

    private function getSingleDependencyGraph()
    {
        $graph = new Graph();
        $vertext1 = $graph->createVertex('v1');
        $vertext2 = $graph->createVertex('v2');
        $vertext3 = $graph->createVertex('v3');

        $vertext1->createEdgeTo($vertext2);
        $vertext1->createEdgeTo($vertext3);
        $vertext2->createEdgeTo($vertext3);

        return $graph;
    }
}