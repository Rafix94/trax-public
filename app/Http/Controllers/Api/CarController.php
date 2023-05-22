<?php

namespace App\Http\Controllers\Api;

use App\Car;
use App\Http\Controllers\Controller;
use App\Http\Resources\CarCollection;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class CarController extends Controller
{

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'make' => 'required',
            'model' => 'required',
            'year' => 'required|numeric',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Create a new car instance with the validated data and user ID
        $car = Car::create([
            'make' => $validatedData['make'],
            'model' => $validatedData['model'],
            'year' => $validatedData['year'],
            'user_id' => $user->id,
        ]);

        // Return a response indicating the successful creation of the car with a status code of 201
        return response()->json([
            'success' => true,
            'message' => 'Car created successfully',
            'data' => new CarResource($car),
        ], Response::HTTP_CREATED);
    }

    public function index()
    {
        $user = Auth::user(); // Get the authenticated user

        // Retrieve all cars associated with the authenticated user
        $cars = Car::ownedByUser($user->id)->get();

        return new CarCollection($cars);
    }

    public function show($id)
    {
        try {

            // Retrieve the car based on the provided ID
            $car = Car::findOrFail($id);

            $this->authorize('view', $car);

            return new CarResource($car);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException('Car not found', $exception);
        }
    }

    public function destroy($id)
    {
        try {
            // Retrieve the car based on the provided ID
            $car = Car::findOrFail($id);

            $this->authorize('delete', $car);

            // Delete the car
            $car->delete();

            // Return a response indicating the successful deletion of the car
            return response()->json([
                'success' => true,
                'message' => 'Car deleted successfully',
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException('Car not found', $exception);
        }
    }

}
