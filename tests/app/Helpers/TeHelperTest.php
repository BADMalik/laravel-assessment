<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\TeHelper;
use Carbon\Carbon;

class TeHelperTest extends TestCase
{
    public function testWillExpireAtLessThan90Hours()
    {
        $due_time = '2024-02-12 18:00:00';
        $created_at = '2024-02-10 12:00:00';

        $result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals('2024-02-12 18:00:00', $result);
    }

    public function testWillExpireAtLessThan24Hours()
    {
        $due_time = '2024-02-12 18:00:00';
        $created_at = '2024-02-12 15:00:00';

        $result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals('2024-02-12 16:30:00', $result);
    }

    public function testWillExpireAtBetween24And72Hours()
    {
        $due_time = '2024-02-14 18:00:00';
        $created_at = '2024-02-12 12:00:00';

        $result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals('2024-02-13 04:00:00', $result);
    }

    public function testWillExpireAtGreaterThan72Hours()
    {
        $due_time = '2024-02-16 18:00:00';
        $created_at = '2024-02-12 12:00:00';

        $result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals('2024-02-14 18:00:00', $result);
    }
}
