<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Airport;

/**
 * @OA\Info(
 *      title="Airplane Ticket Reservation API",
 *      version="1.0.0",
 *      description="API for managing airports and airplane tickets",
 *      @OA\Contact(
 *          email="support@example.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Local Development Server"
 * )
 */
class AirportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/airports",
     *     summary="Get all airports",
     *     tags={"Airports"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(): JsonResponse
    {
        $airports = Airport::all();
        return response()->json($airports);
    }

    /**
     * @OA\Post(
     *     path="/api/airports",
     *     summary="Add a new airport",
     *     tags={"Airports"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name", "country"},
     *             @OA\Property(property="code", type="string", example="CDG", description="Airport IATA code (3-letter)"),
     *             @OA\Property(property="name", type="string", example="Charles de Gaulle Airport", description="Full name of the airport"),
     *             @OA\Property(property="country", type="string", example="France", description="Country where the airport is located")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Airport successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="code", type="string", example="CDG"),
     *             @OA\Property(property="name", type="string", example="Charles de Gaulle Airport"),
     *             @OA\Property(property="country", type="string", example="France"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-10T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-10T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The code field is required.")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:airports,code',
            'name' => 'required|string',
            'country' => 'required|string'
        ]);

        $airport = Airport::create($validated);

        return response()->json($airport, 201);
    }
}
