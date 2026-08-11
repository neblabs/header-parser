# Neblabs Header Parser

A lightweight PHP library designed to parse WordPress-style file headers from both PHP block comments (`/* ... */`) and Markdown documents (`=== Title ===`).

In addition to extracting key-value header metadata, it tracks exact byte offsets and line boundaries for the parsed content, making it ideal for build tools, code analyzers, and metadata extractors.

---

## Features

* **Memory efficient**: Does not load the whole content into an array of lines.
* **PHP Block Comment Parsing:** Extracts `Key: Value` metadata from multi-line `/* ... */` comment blocks.
* **Supports any position:** Header blocks can be anywhere in the string, not just at the beginning. Parses the first it finds.
* **Markdown Header Parsing:** Parses metadata blocks starting with a heading (`=== ... ===`) up to double line breaks.
* **Boundary Tracking:** Returns exact start/end byte offsets (`innerStart`, `innerEnd`) and 0-indexed line numbers (`innerStartLine`, `innerEndLine`).
* **Flexible Keys:** Supports custom arbitrary key names with varying structures including spaces, dashes, and underscores.

---

## Installation

Install the package via Composer:

```bash
composer require neblabs/header-parser

```

---

## Usage

Import the `parse` function and supply the content string along with the mode (`'php'` or `'md'`).

### 1. Parsing PHP Comment Headers

Extract headers from a standard WordPress plugin or theme file header block. Block can be found anywhere; supports symbol imports.

```php
<?php

use function Neblabs\HeaderParser\parse;

$phpContent = <<<PHP
<?php
/*
 * Plugin Name:       Coupons+
 * Description:       Next-generation coupon offers engine for WooCommerce.
 * Version:           1.0.0
 * Author:            neblabs
 * Text Domain:       coupons-plus
 */
PHP;

$data = parse($phpContent, 'php');

// Access parsed headers
print_r($data->values);
/*
Array
(
    [Plugin Name] => Coupons+
    [Description] => Next-generation coupon offers engine for WooCommerce.
    [Version] => 1.0.0
    [Author] => neblabs
    [Text Domain] => coupons-plus
)
*/

// $data->boundaries
$data->boundaries->innerStart;     // (int) Starting byte offset of header body
$data->boundaries->innerEnd;       // (int) Ending byte offset of header body
$data->boundaries->innerStartLine; // (int) Starting line number
$data->boundaries->innerEndLine;   // (int) Ending line number

```

### 2. Parsing Markdown Headers

Extract headers from a WordPress-style `readme.txt` or Markdown header block:

```php
<?php

use function Neblabs\HeaderParser\parse;

$markdownContent = <<<MD
=== Coupons+ ===
Plugin Name:       Coupons+
Description:       Next-generation coupon offers engine for WooCommerce.
Version:           1.0.0
Author:            neblabs

This description is outside of the header boundaries!
MD;

$data = parse($markdownContent, 'md');

print_r($data->values);

```

---

## Output Data Structure

The `parse()` function returns a result object containing the extracted `values` and offset `boundaries`.

### Data Object Properties

```php
// $data->values
[
    'Plugin Name'     => 'Coupons+',
    'Description'     => 'Next-generation coupon offers engine for WooCommerce.',
    'Version'         => 'dev',
    'Author'          => 'neblabs',
    'Author URI'      => 'unknown',
    'Text Domain'     => 'coupons-plus-for-woocommerce',
    'Domain Path'     => '/international',
    'another-key'     => 'another:value!',
    'with_underscore' => 'under the score.',
];

// $data->boundaries
$data->boundaries->innerStart;     // (int) Starting byte offset of header body
$data->boundaries->innerEnd;       // (int) Ending byte offset of header body
$data->boundaries->innerStartLine; // (int) Starting line number
$data->boundaries->innerEndLine;   // (int) Ending line number

```

## Boundaries

0-based from the beginning of the given string. Illustrated here inside brackets eg. [innerStart].

### PHP
```php

$phpContent = <<<PHP
<?php
/*[innerStart]
 [innerStartLine]* Plugin Name:       Coupons+
 * Description:       Next-generation coupon offers engine for WooCommerce.
 * Version:           1.0.0
 * Author:            neblabs
 [innerEndLine]* Text Domain:       coupons-plus
 [innerEnd]*/
PHP;

$data->boundaries->innerStart;     // (int) Starting byte offset of header body
$data->boundaries->innerEnd;       // (int) Ending byte offset of header body
$data->boundaries->innerStartLine; // (int) Starting line number
$data->boundaries->innerEndLine;   // (int) Ending line number

```
```php
<?php

use function Neblabs\HeaderParser\parse;

$markdownContent = <<<MD
=== Coupons+ ===[innerStart]
[innerStartLine]Plugin Name:       Coupons+
Description:       Next-generation coupon offers engine for WooCommerce.
Version:           1.0.0
[innerEndLine]Author:            neblabs[innerEnd]

This description is outside of the header boundaries!
MD;
```

```php

$data->boundaries->innerStart;     // (int) Starting byte offset of header body
$data->boundaries->innerEnd;       // (int) Ending byte offset of header body
$data->boundaries->innerStartLine; // (int) Starting line number
$data->boundaries->innerEndLine;   // (int) Ending line number

```

## License

The MIT License (MIT).