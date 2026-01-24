<?php

namespace Tests\Unit;

use App\Services\ArticleService;
use PHPUnit\Framework\TestCase;

class ArticleServiceReadTimeTest extends TestCase
{
    public function test_estimate_read_time_rounds_up_and_has_minimum_one(): void
    {
        $service = new ArticleService();

        $oneWord = 'Hello';
        $this->assertSame(1, $service->estimateReadTime($oneWord));

        $twoHundredWords = implode(' ', array_fill(0, 200, 'word'));
        $this->assertSame(1, $service->estimateReadTime($twoHundredWords));

        $twoHundredOneWords = implode(' ', array_fill(0, 201, 'word'));
        $this->assertSame(2, $service->estimateReadTime($twoHundredOneWords));
    }
}
