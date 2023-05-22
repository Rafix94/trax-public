<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;


class TripController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $carIds = DB::table('cars')
            ->where('user_id', $user->id)
            ->pluck('id')
            ->toArray(); // Retrieve car IDs owned by the user

        $trips = [];

        if (!empty($carIds)) {
            $trips = DB::table('trips')
                ->whereIn('car_id', $carIds)
                ->join('cars', 'trips.car_id', '=', 'cars.id')
                ->select('trips.id', 'trips.date', 'trips.miles', 'cars.id as car_id', 'cars.make', 'cars.model', 'cars.year')
                ->orderBy('trips.date', 'desc')
                ->get();

            $trips = $this->calculateMilageForTrips($trips);
        }

        return response()->json([
            'success' => true,
            'data' => $trips,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'miles' => 'required|numeric',
            'car_id' => 'required|exists:cars,id',
        ]);

        // Convert the date string to the correct format
        $date = Carbon::parse($validatedData['date'])->format('Y-m-d');


        $user = Auth::user();

        $trip = Trip::create([
            'date' => $date,
            'miles' => $validatedData['miles'],
            'car_id' => $validatedData['car_id'],
            'total' => 0,
            'user_id' => $user->id,
        ]);

        // Return a response indicating the successful creation of the trip
        return response()->json([
            'success' => true,
            'message' => 'Trip created successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * @param \Illuminate\Support\Collection $trips
     * @return \Illuminate\Support\Collection
     */
    public function calculateMilageForTrips(\Illuminate\Support\Collection $trips): \Illuminate\Support\Collection
    {
        $total = $trips->sum('miles');
        $trips = $trips->map(function ($trip) use (&$total) {
            $mappedTrip = [
                'id' => $trip->id,
                'date' => Carbon::parse($trip->date)->format('m/d/Y'),
                'miles' => $trip->miles,
                'total' => $total,
                'car' => [
                    'id' => $trip->car_id,
                    'make' => $trip->make,
                    'model' => $trip->model,
                    'year' => $trip->year,
                ],
            ];
            $total = round($total - $trip->miles, 2);
            return $mappedTrip;
        });
        return $trips;

    }
}
