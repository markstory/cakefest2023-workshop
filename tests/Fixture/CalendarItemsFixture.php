<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CalendarItemsFixture
 */
class CalendarItemsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'start_date' => '2023-06-20',
                'end_date' => '2023-06-20',
                'start_time' => '03:10:02',
                'end_time' => '03:10:02',
                'timezone' => 'Lorem ipsum dolor sit amet',
                'recurs_on' => 'Lorem ipsum dolor sit amet',
                'created' => '2023-06-20 03:10:02',
                'modified' => '2023-06-20 03:10:02',
            ],
        ];
        parent::init();
    }
}
