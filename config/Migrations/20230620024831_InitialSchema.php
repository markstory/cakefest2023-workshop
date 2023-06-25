<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class InitialSchema extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('calendar_items');
        $table->addColumn('title', 'string', ['null' => false, 'limit' => null])
            ->addColumn('notes', 'text')
            ->addColumn('start_date', 'date')
            ->addColumn('end_date', 'date')
            ->addColumn('start_time', 'time')
            ->addColumn('end_time', 'time')
            ->addColumn('timezone', 'string', ['null' => false, 'default' => 'UTC'])
            ->addColumn('recurs_on', 'string')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => false,
            ]);
        $table->create();

        $table = $this->table('articles');
        $table->addColumn('title', 'string', ['null' => false, 'limit' => null])
            ->addColumn('markdown', 'text')
            ->addColumn('html', 'text')
            ->addColumn('status', 'string')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => false,
            ])
            ->addIndex('status');
        $table->create();
    }
}
