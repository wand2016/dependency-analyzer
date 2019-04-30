<?php
declare(strict_types=1);

namespace DependencyAnalyzer\Inspector\RuleViolationDetector;

use DependencyAnalyzer\DependencyGraph\StructuralElementPatternMatcher;

class Component
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var StructuralElementPatternMatcher
     */
    protected $matcher;

    /**
     * @var StructuralElementPatternMatcher
     */
    protected $dependerMatcher;

    /**
     * @var StructuralElementPatternMatcher
     */
    protected $dependeeMatcher;

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(
        string $name,
        StructuralElementPatternMatcher $pattern,
        StructuralElementPatternMatcher $dependerPatterns = null,
        StructuralElementPatternMatcher $dependeePatterns = null
    ) {
        $this->name = $name;
        $this->matcher = $pattern;
        $this->dependerMatcher = $dependerPatterns;
        $this->dependeeMatcher = $dependeePatterns;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefineMatcher(): StructuralElementPatternMatcher
    {
        return $this->matcher;
    }

    public function isBelongedTo(string $className): bool
    {
        return $this->matcher->isMatch($className);
    }

    public function verifyDepender(string $className): bool
    {
        if ($this->isBelongedTo($className)) {
            return true;
        } elseif (is_null($this->dependerMatcher)) {
            return true;
        }

        return $this->dependerMatcher->isMatch($className);
    }

    public function verifyDependee(string $className): bool
    {
        if ($this->isBelongedTo($className)) {
            return true;
        } elseif (is_null($this->dependeeMatcher)) {
            return true;
        }

        return $this->dependeeMatcher->isMatch($className);
    }

    /**
     * @param string $className
     * @param StructuralElementPatternMatcher[] $patterns
     * @return bool
     */
    protected function checkPatterns(string $className, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($pattern->isMatch($className)) {
                return true;
            }
        }

        return false;
    }

    public function setAttribute(string $key, $name): void
    {
        $this->attributes[$key] = $name;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function toArray()
    {
        $ret = [
            'define' => $this->matcher->toArray()
        ];

        if (!is_null($this->dependerMatcher)) {
            $ret['depender'] = $this->dependerMatcher->toArray();
        }
        if (!is_null($this->dependeeMatcher)) {
            $ret['dependee'] = $this->dependeeMatcher->toArray();
        }

        return $ret;
    }
}
