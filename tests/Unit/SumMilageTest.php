<?php
namespace Tests\Unit;

use App\Http\Controllers\Api\TripController;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class TripControllerTest extends TestCase
{
    public function testCalculateMilageForTripsReturnsMappedTripsWithCorrectValues()
    {
        $trips = new Collection([
            (object) [
                'id' => 1,
                'date' => '2022-01-01',
                'miles' => 100,
                'car_id' => 1,
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2022,
            ],
            (object) [
                'id' => 2,
                'date' => '2022-02-01',
                'miles' => 200,
                'car_id' => 2,
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
            ],
            (object) [
                'id' => 3,
                'date' => '2022-03-01',
                'miles' => 150,
                'car_id' => 1,
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2022,
            ],
        ]);

        $tripController = new TripController();
        $mappedTrips = $tripController->calculateMilageForTrips($trips);

        $expectedMappedTrips = new Collection([
            [
                'id' => 1,
                'date' => Carbon::parse('2022-01-01')->format('m/d/Y'),
                'miles' => 100,
                'total' => 450,
                'car' => [
                    'id' => 1,
                    'make' => 'Toyota',
                    'model' => 'Corolla',
                    'year' => 2022,
                ],
            ],
            [
                'id' => 2,
                'date' => Carbon::parse('2022-02-01')->format('m/d/Y'),
                'miles' => 200,
                'total' => 350,
                'car' => [
                    'id' => 2,
                    'make' => 'Honda',
                    'model' => 'Civic',
                    'year' => 2021,
                ],
            ],
            [
                'id' => 3,
                'date' => Carbon::parse('2022-03-01')->format('m/d/Y'),
                'miles' => 150,
                'total' => 150,
                'car' => [
                    'id' => 1,
                    'make' => 'Toyota',
                    'model' => 'Corolla',
                    'year' => 2022,
                ],
            ],
        ]);

        $this->assertEquals($expectedMappedTrips, $mappedTrips);
    }
}
