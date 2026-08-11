<?php

use PHPUnit\Framework\TestCase;
use function Neblabs\HeaderParser\parse;

class ParserTest extends TestCase
{
    public array $expects = [
        'Plugin Name' => 'Coupons+',
        'Description' => 'Next-generation coupon offers engine for WooCommerce. Create advanced deals, smart BOGO offers, and more!',
        'Version' => 'dev',
        'Author' => 'neblabs',
        'Author URI' => 'unknown',
        'Text Domain' => 'coupons-plus-for-woocommerce',
        'Domain Path' => '/international',
        'another-key' => 'another:value!',
        'with_underscore' => 'under the score.',
    ];

    public function test_comments()
    {
        $comments = <<<COMMENTS
<?php
use CouponsPlus\Original\Events\Registrator\EventsRegistrator;
use CouponsPlus\Original\Installation;

/*
 * Plugin Name:       Coupons+
 * Description:       Next-generation coupon offers engine for WooCommerce. Create advanced deals, smart BOGO offers, and more!
 * Version:           dev
 * Author:            neblabs
 * Author URI:        unknown
 * Text Domain:       coupons-plus-for-woocommerce
 * Domain Path:       /international
 * another-key:       another:value!
 * with_underscore:   under the score.
 */

outside boundaries: no!
COMMENTS;
        $data = parse($comments, 'php');
        $this->assertEquals($this->expects, $data->values);
        $this->assertSame(111, $data->boundaries->innerStart);
        $this->assertSame(522, $data->boundaries->innerEnd);
        $this->assertSame(4, $data->boundaries->innerStartLine);
        $this->assertSame(14, $data->boundaries->innerEndLine);
    }

    public function test_markdown()
    {
        $md = <<<MD
=== Coupons+ ===
Plugin Name:       Coupons+
Description:       Next-generation coupon offers engine for WooCommerce. Create advanced deals, smart BOGO offers, and more!
Version:           dev
Author:            neblabs
Author URI:        unknown
Text Domain:       coupons-plus-for-woocommerce
Domain Path:       /international
another-key:       another:value!
with_underscore:   under the score.

This is a description: outside of the boundaries!
MD;

        $data = parse($md, 'md');
        $this->assertEquals($this->expects, $data->values);
        $this->assertSame(3, $data->boundaries->innerStart);
        $this->assertSame(398, $data->boundaries->innerEnd);
        $this->assertSame(0, $data->boundaries->innerStartLine);
        $this->assertSame(10, $data->boundaries->innerEndLine);
    }
}