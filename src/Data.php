<?php


namespace Neblabs\HeaderParser;

readonly class Data
{
    public function __construct(
        public array      $values,
        public Boundaries $boundaries
    ) {}
}