<?php

namespace Neblabs\HeaderParser;

/**
 * Memory-efficient generator that yields lines one by one,
 * preserving empty lines and correct line numbers.
 */
function getLines(string $content): \Generator
{
    $offset = 0;
    $length = strlen($content);

    while ($offset < $length) {
        $nextPos = strpos($content, "\n", $offset);
        if ($nextPos === false) {
            yield substr($content, $offset);
            break;
        }
        yield substr($content, $offset, $nextPos - $offset);
        $offset = $nextPos + 1;
    }
}

/**
 * @param string $content The contents to parse
 */
function parse(string $content, string $syntax): Data
{
    if ($syntax === 'php') {
        $startBoundaryInner = '/*';
        $endBoundaryInner = '*/';
    } else {
        $startBoundaryInner = '===';
        $endBoundaryInner = "\n\n";
    }

    $startBoundaryInnerLine = false;
    $endBoundaryInnerLine = false;
    $items = [];

    foreach (getLines($content) as $lineNo => $line) {
        $line = rtrim($line, "\r");

        // 1. Find start boundary
        if ($startBoundaryInnerLine === false) {
            if (strpos($line, $startBoundaryInner) !== false) {
                $startBoundaryInnerLine = $lineNo;
            }
            continue; // Keep scanning until inner boundary is reached
        }

        // 2. Find end boundary (if boundary is \n, check for empty line)
        $isEndBoundary = ($endBoundaryInner === "\n\n")
            ? trim($line) === ''
            : strpos($line, $endBoundaryInner) !== false;

        if ($endBoundaryInnerLine === false && $isEndBoundary) {
            $endBoundaryInnerLine = $lineNo;
            break; // Stop parsing immediately
        }

        // 3. Process key: value pairs inside the header block
        if (strpos($line, ':') !== false) {
            $lineParts = explode(':', $line);
            $left = array_shift($lineParts);
            $right = implode(':', $lineParts);

            $matches = [];
            if (preg_match('/(\w[\w\s\-]*)/u', $left, $matches)) {
                $items[trim($matches[1])] = trim($right);
            }
        }
    }

    return new Data(
        values: $items,
        boundaries: new Boundaries(
            innerStart: strpos($content, $startBoundaryInner) + strlen($startBoundaryInner),
            innerEnd: strpos($content, $endBoundaryInner),
            innerStartLine: $startBoundaryInnerLine,
            innerEndLine: $endBoundaryInnerLine,
        )
    );
}