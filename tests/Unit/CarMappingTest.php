<?php
namespace Tests\Unit\Resources;

use App\Http\Resources\CarCollection;
use App\Http\Resources\CarResource;
use App\Car;
use App\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CarResourceTest extends TestCase
{
    public function testCarResourceTransformsDataCorrectly()
    {
        $car = new Car([
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
        ]);

        $car->id = 1;

        $trip1 = new Trip([
            'miles' => 100,
        ]);

        $trip2 = new Trip([
            'miles' => 200,
        ]);

        $car->trips = new Collection([$trip1, $trip2]);

        $request = Request::create('/');

        $resource = new CarResource($car);
        $transformedData = $resource->toArray($request);

        $expectedData = [
            'id' => 1,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'trip_count' => 2,
            'trip_miles' => 300,
        ];

        $this->assertEquals($expectedData, $transformedData);
    }
}

class CarCollectionTest extends TestCase
{
    public function testCarCollectionTransformsDataCorrectly()
    {
        $car1 = new Car([
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
        ]);
        $car1->id = 1;

        $trip1 = new Trip([
            'id' => 1,
            'miles' => 100,
        ]);

        $car1->trips = new Collection([$trip1]);

        $car2 = new Car([
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
        ]);

        $car2->id = 2;

        $trip2 = new Trip([
            'id' => 2,
            'miles' => 200,
        ]);

        $trip3 = new Trip([
            'id' => 3,
            'miles' => 150,
        ]);

        $car2->trips = new Collection([$trip2, $trip3]);

        $cars = new Collection([$car1, $car2]);

        $request = Request::create('/');

        $collection = new CarCollection($cars);
        $transformedData = $collection->toArray($request);

        $expectedData = [
            [
                'id' => 1,
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2022,
                'trip_count' => 1,
                'trip_miles' => 100,
            ],
            [
                'id' => 2,
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
                'trip_count' => 2,
                'trip_miles' => 350,
            ],
        ];

        $this->assertEquals($expectedData, $transformedData);
    }
}
