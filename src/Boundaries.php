<?php

namespace Neblabs\HeaderParser;

class Boundaries
{
    public function __construct(
        public readonly int $innerStart,
        public readonly int $innerEnd,
        public readonly int $innerStartLine,
        public readonly int $innerEndLine,
    ) {}
}