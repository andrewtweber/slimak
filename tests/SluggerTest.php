<?php

namespace Slimak\Tests;

use Slimak\Support\Slugger;

/**
 * Class SluggerTest
 *
 * @package Slimak\Tests
 */
class SluggerTest extends TestCase
{
    /**
     * @test
     *
     * @param string|null $input
     * @param string|null $output
     *
     * @testWith [null,           null]
     *           ["",             null]
     *           ["  ",           null]
     *           ["--",           null]
     *           ["123",          "123"]
     *           ["Slug",         "Slug"]
     *           ["Slug Test",    "Slug-Test"]
     *           ["Slug   Test",  "Slug-Test"]
     *           [" Slug Test ",  "Slug-Test"]
     *           ["   Slug Test", "Slug-Test"]
     *           ["Slug Test   ", "Slug-Test"]
     *           ["Slug---Test",  "Slug-Test"]
     *           ["^*Slug#?Test", "Slug-Test"]
     *           ["Slug- -Test",  "Slug-Test"]
     *           ["Slug Test 1",  "Slug-Test-1"]
     */
    public function output_with_defaults(?string $input, ?string $output)
    {
        $this->assertSame($output, Slugger::slugify($input));
    }

    /**
     * @test
     *
     * @param string|null $input
     * @param string|null $output
     *
     * @testWith [null,           null]
     *           ["",             null]
     *           ["  ",           null]
     *           ["--",           null]
     *           ["123",          "123"]
     *           ["Slug",         "slug"]
     *           ["Slug Test",    "slug-test"]
     *           ["Slug   Test",  "slug-test"]
     *           [" Slug Test ",  "slug-test"]
     *           ["   Slug Test", "slug-test"]
     *           ["Slug Test   ", "slug-test"]
     *           ["Slug---Test",  "slug-test"]
     *           ["^*Slug#?Test", "slug-test"]
     *           ["Slug- -Test",  "slug-test"]
     *           ["Slug Test 1",  "slug-test-1"]
     */
    public function output_with_convert_to_lowercase(?string $input, ?string $output)
    {
        $this->assertSame($output, Slugger::slugify($input, true));
    }

    /**
     * @test
     *
     * @param string|null $input
     * @param string|null $output
     *
     * @testWith [null,           null]
     *           ["",             null]
     *           ["  ",           null]
     *           ["--",           null]
     *           ["123",          "123"]
     *           ["Slug",         "Slug"]
     *           ["Slug Test",    "Slug_Test"]
     *           ["Slug   Test",  "Slug_Test"]
     *           [" Slug Test ",  "Slug_Test"]
     *           ["   Slug Test", "Slug_Test"]
     *           ["Slug Test   ", "Slug_Test"]
     *           ["Slug---Test",  "Slug_Test"]
     *           ["^*Slug#?Test", "Slug_Test"]
     *           ["Slug- -Test",  "Slug_Test"]
     *           ["Slug Test 1",  "Slug_Test_1"]
     */
    public function output_with_custom_glue(?string $input, ?string $output)
    {
        $this->assertSame($output, Slugger::slugify($input, false, '_'));
    }

    /**
     * @test
     *
     * @param string|null $input
     * @param string|null $output
     *
     * @testWith ["Slug's Test",   "Slugs-Test"]
     *           ["'Slug' Test",   "Slug-Test"]
     *           ["Slug'' Test",   "Slug-Test"]
     *           ["Slug&Test",     "Slug-and-Test"]
     *           ["Slug & Test",   "Slug-and-Test"]
     *           ["Slug'&'Test",   "Slug-and-Test"]
     *           ["Slug  &  Test", "Slug-and-Test"]
     *           ["Slug && Test",  "Slug-and-and-Test"]
     */
    public function conversions(?string $input, ?string $output)
    {
        $this->assertSame($output, Slugger::slugify($input));
    }

    /**
     * @test
     *
     * Note: some of the chars in the last test are intentionally not transliterated
     * even though they look like upside-down letters. These are used for pronunciation
     * and not in any language.
     *
     * @param string|null $input
     * @param string|null $output
     *
     * @testWith ["A æ Übérmensch", "A-ae-Ubermensch"]
     *           ["på høyeste nivå!", "pa-hoyeste-niva"]
     *           ["И я люблю PHP! ﬁ", "I-a-lublu-PHP-fi"]
     *           ["ẞ ß ʃaʁfəs ʔɛs", "SS-ss-a-f-s-es"]
     */
    public function accented_characters_are_transliterated(?string $input, ?string $output)
    {
        $this->assertSame($output, Slugger::slugify($input));
    }
}
