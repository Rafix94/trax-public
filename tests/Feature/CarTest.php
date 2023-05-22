<?php

namespace Tests\Feature;

use App\Car;
use App\Trip;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class CarControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker; // Generate fake data

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $user = User::factory()->create();

        // Create a car associated with the user
        $this->car = Car::factory()->for($user)->create();

        // Create trips associated with the car
        $this->trips = Trip::factory()->count(3)->for($this->car)->create();
    }

    public function testCarIndex()
    {
        // Create a personal access client for the user
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            $this->car->user_id, 'TestToken', 'http://localhost'
        );
        $token = $this->car->user->createToken('TestToken', [])->accessToken;

        // Set the Authorization header with the access token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send a GET request to the index endpoint
        $response = $this->getJson('/api/car', $headers);

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data') // Check if there is 1 car returned in the response
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'make',
                        'model',
                        'year',
                        'trip_count',
                        'trip_miles',
                    ],
                ],
            ])
            ->assertJson([
                'data' => [
                    [
                        'id' => $this->car->id,
                        'make' => $this->car->make,
                        'model' => $this->car->model,
                        'year' => $this->car->year,
                        'trip_count' => $this->car->trips->count(),
                        'trip_miles' => $this->car->trips->sum('miles'),
                    ],
                ],
            ]);
    }


    public function testCarCreation()
    {
        // Create a personal access client for the user
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            $this->car->user_id, 'TestToken', 'http://localhost'
        );
        $token = $this->car->user->createToken('TestToken', [])->accessToken;

        // Set the Authorization header with the access token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send the request with the headers
        $response = $this->postJson('/api/car', [
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
        ], $headers);

        // Assert the response
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Car created successfully',
            ]);

        // Assert that the new car exists in the database
        $this->assertDatabaseHas('cars', [
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
        ]);
    }

    public function testCarDeletionWithTrips()
    {
        // Create a personal access client for the user
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            $this->car->user_id, 'TestToken', 'http://localhost'
        );
        $token = $this->car->user->createToken('TestToken', [])->accessToken;

        // Set the Authorization header with the access token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send the request with the headers to delete the car
        $response = $this->deleteJson('/api/car/' . $this->car->id, [], $headers);

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Car deleted successfully',
            ]);

        // Assert that the car and associated trips are deleted from the database
        $this->assertDatabaseMissing('cars', ['id' => $this->car->id]);
        $this->assertDatabaseMissing('trips', ['car_id' => $this->car->id]);
    }

    public function testUnauthorizedCarCreation()
    {
        // Send the request without the headers or with invalid headers
        $response = $this->postJson('/api/car', [
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
        ], []);

        // Assert the response
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testForbiddenCarDeletionWithTrips()
    {
        $otherUser = User::factory()->create();

        // Create a personal access client for the user
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            $otherUser->id, 'TestToken', 'http://localhost'
        );
        $token = $otherUser->createToken('TestToken', [])->accessToken;

        // Set the Authorization header with the access token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send the request with the headers to delete the car
        $response = $this->deleteJson('/api/car/' . $this->car->id, [], $headers);

        // Assert the response
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        // Assert that the car still exists in the database
        $this->assertDatabaseHas('cars', ['id' => $this->car->id]);

        // Assert that the trips associated with the car still exist in the database
        $this->assertDatabaseHas('trips', ['car_id' => $this->car->id]);
    }
}
