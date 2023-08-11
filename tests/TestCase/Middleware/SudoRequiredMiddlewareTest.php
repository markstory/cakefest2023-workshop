<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use App\Middleware\SudoRequiredMiddleware;
use Cake\TestSuite\TestCase;

/**
 * App\Middleware\SudoRequiredMiddleware Test Case
 */
class SudoRequiredMiddlewareTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Middleware\SudoRequiredMiddleware
     */
    protected $SudoRequired;

    /**
     * Test process method
     *
     * @return void
     * @uses \App\Middleware\SudoRequiredMiddleware::process()
     */
    public function testProcess(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
