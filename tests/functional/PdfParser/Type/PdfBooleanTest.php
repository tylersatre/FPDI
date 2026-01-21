<?php

namespace setasign\Fpdi\functional\PdfParser\Type;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use setasign\Fpdi\PdfParser\Type\PdfBoolean;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;

class PdfBooleanTest extends TestCase
{
    public static function createProvider()
    {
        $data = [
            ['true', true],
            ['false', true],
            ['3454', true],
            [false, false],
        ];

        return $data;
    }

    #[DataProvider('createProvider')]
    public function testCreate($in, $expectedResult)
    {
        $result = PdfBoolean::create($in);
        $this->assertInstanceOf(PdfBoolean::class, $result);
        $this->assertSame($expectedResult, $result->value);
    }
}
