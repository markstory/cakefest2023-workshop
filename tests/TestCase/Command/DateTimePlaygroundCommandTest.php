<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\DateTimePlaygroundCommand;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Command\DateTimePlaygroundCommand Test Case
 *
 * @uses \App\Command\DateTimePlaygroundCommand
 */
class DateTimePlaygroundCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @uses \App\Command\DateTimePlaygroundCommand::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test execute method
     *
     * @return void
     * @uses \App\Command\DateTimePlaygroundCommand::execute()
     */
    public function testExecute(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
