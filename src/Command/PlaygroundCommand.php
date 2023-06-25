<?php
declare(strict_types=1);

namespace App\Command;

use App\Model\ArticleStatus;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * DateTimePlayground command.
 */
class PlaygroundCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->addOption('reset', [
            'help' => 'Reset the database state',
            'boolean' => true,
            'default' => false,
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        if ($args->getOption('reset')) {
            return $this->reset($args, $io);
        }

        // $this->datetimeTypes($args, $io);
        $this->enumTypes($args, $io);
    }

    /**
     * Delete all records in the local database and create new state.
     */
    protected function reset(Arguments $args, ConsoleIo $io) {
        $calendarItems = $this->fetchTable('CalendarItems');

        $calendarItems->deleteQuery()->where('1=1')->execute();

        $entity = $calendarItems->newEntity([
            'title' => 'All day event',
            'start_date' => '2023-09-28',
            'end_date' => '2023-09-30',
        ]);
        $calendarItems->saveOrFail($entity);

        $entity = $calendarItems->newEntity([
            'title' => 'Date time event',
            'start_date' => '2023-09-28',
            'end_date' => '2023-09-30',
            'start_time' => '10:30',
            'end_time' => '11:00',
        ]);
        $calendarItems->saveOrFail($entity);

        $entity = $calendarItems->newEntity([
            'title' => 'Time only',
            'start_time' => '10:30',
            'end_time' => '11:00',
            'recurs_on' => '+7 days',
        ]);
        $calendarItems->saveOrFail($entity);

        $articles = $this->fetchTable('Articles');
        $article = $articles->newEntity([
            'title' => 'First post',
            'markdown' => 'This is my first post',
            'status' => ArticleStatus::DRAFT->value,
        ]);
        $articles->saveOrFail($article);
        $io->success('Reset complete!');

        return self::CODE_SUCCESS;
    }

    protected function enumTypes($args, $io) {
        $articles = $this->fetchTable('Articles');

        $article = $articles->find()->firstOrFail();
        eval(breakpoint());
    }

    protected function datetimeTypes(Arguments $args, ConsoleIo $io)
    {
        $calendarItems = $this->fetchTable('CalendarItems');

        // {{{ Load a date record up and look at the types.
        // $date->start_date is a date.
        // It is not a datetime
        // it has no time components.
        // while it has many similar methods to datetime
        // format() modify(), addDay(), addMonth()
        // it cannot be modified by time.
        // great for calendar days when you don't
        // want to worry about time or timezones mucking up your math
        // }}}
        $day = $calendarItems->findByTitle('All day event')->firstOrFail();
        eval(breakpoint());

        // {{{ Load a time record up and look at the types.
        // $date->start_time is a time.
        // It is not a datetime
        // it has no date components.
        // while it has many similar methods to datetime
        // format() modify(), addHour(), addMinute()
        // it cannot be modified by day.
        // great for clocks, or watches.
        // want to worry about time or timezones mucking up your math
        // also worth looking at the datetime properties.
        // }}}
        $time = $calendarItems->findByTitle('Time only')->firstOrFail();
        eval(breakpoint());
    }
}
