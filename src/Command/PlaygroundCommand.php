<?php
declare(strict_types=1);

namespace App\Command;

use App\Model\ArticleStatus;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Text;

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
        $parser->addOption('name', [
            'help' => 'The name thing',
            'default' => '',
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
        // $this->enumTypes($args, $io);
        // $this->queryClasses($args, $io);
        // $this->typedFinder($args, $io);
        // $this->consoleArgs($args, $io);
    }

    /**
     * Delete all records in the local database and create new state.
     */
    protected function reset(Arguments $args, ConsoleIo $io) {
        $users = $this->fetchTable('Users');
        $users->deleteQuery()->where('1=1')->execute();
        $user = $users->newEntity([
            'email' => 'mark@example.com',
            'name' => 'Mark',
        ]);
        $user->password = 'cakefest2023';
        $user->uuid = Text::uuid();
        $user = $users->saveOrFail($user);

        $other = $users->newEntity([
            'email' => 'admad@example.com',
            'name' => 'ADmad',
        ]);
        $other->uuid = Text::uuid();
        $other->password = 'correct horse battery stapler';
        $users->saveOrFail($other);

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
        $articles->deleteQuery()->where('1=1')->execute();

        $article = $articles->newEntity([
            'title' => 'First post',
            'user_id' => $user->id,
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

        $time = $calendarItems->findByTitle('Time only')->firstOrFail();
        $day = $calendarItems->findByTitle('All day event')->firstOrFail();
        eval(breakpoint());
    }

    public function queryClasses(Arguments $args, ConsoleIo $io)
    {
        $articles = $this->fetchTable('Articles');
        eval(breakpoint());
    }

    public function typedFinder(Arguments $args, ConsoleIo $io)
    {
        $articles = $this->fetchTable('Articles');
        eval(breakpoint());
    }

    // {{{
    // Show arguments and options
    // > Can use -- to add positional arguments.
    // bin/cake playground --name mark -- one two three "four and"
    //
    // }}}
    public function consoleArgs(Arguments $args, ConsoleIo $io)
    {
        eval(breakpoint());
    }
}
