<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Airport;

class TicketController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/tickets",
     *     summary="Get all booked tickets",
     *     tags={"Tickets"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all tickets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="passport_id", type="string", example="A1234567", description="Passenger's passport ID"),
     *                 @OA\Property(property="source_airport", type="string", example="JFK", description="IATA code of the source airport"),
     *                 @OA\Property(property="destination_airport", type="string", example="LAX", description="IATA code of the destination airport"),
     *                 @OA\Property(property="departure_time", type="string", format="date-time", example="2025-03-10T12:00:00", description="Scheduled departure time"),
     *                 @OA\Property(property="aircraft_number", type="string", example="AA101", description="Aircraft flight number"),
     *                 @OA\Property(property="seat", type="string", example="B12", description="Seat assigned to the passenger"),
     *                 @OA\Property(property="status", type="string", enum={"booked", "cancelled"}, example="booked", description="Ticket status"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-10T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-10T12:00:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $airports = Ticket::all();
        return response()->json($airports);
    }

    /**
     * @OA\Post(
     *     path="/api/tickets",
     *     summary="Book a new ticket",
     *     tags={"Tickets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"passport_id", "source_airport", "destination_airport", "departure_time", "aircraft_number"},
     *             @OA\Property(property="passport_id", type="string", example="A1234567"),
     *             @OA\Property(property="source_airport", type="string", example="JFK"),
     *             @OA\Property(property="destination_airport", type="string", example="LAX"),
     *             @OA\Property(property="departure_time", type="string", format="date-time", example="2025-03-10T12:00:00"),
     *             @OA\Property(property="aircraft_number", type="string", example="AA101")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ticket successfully created"),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid airport code",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid source or destination airport code")
     *         )
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'passport_id' => 'required|string',
            'source_airport' => 'required|string',
            'destination_airport' => 'required|string|different:source_airport',
            'departure_time' => 'required|date',
            'aircraft_number' => 'required|string'
        ]);

        // Convert airport codes to IDs
        $sourceAirportId = Airport::where('code', $validated['source_airport'])->value('id');
        $destinationAirportId = Airport::where('code', $validated['destination_airport'])->value('id');

        // 🚨 Ensure the airports exist before proceeding
        if (!$sourceAirportId || !$destinationAirportId) {
            return response()->json(['error' => 'Invalid source or destination airport code'], 400);
        }

        // Generate a unique seat
        do {
            $seat = chr(rand(65, 68)) . rand(1, 32);
        } while (Ticket::where('aircraft_number', $validated['aircraft_number'])
            ->where('departure_time', $validated['departure_time'])
            ->where('seat', $seat)
            ->exists());

        // Create the ticket
        $ticket = Ticket::create([
            'passport_id' => $validated['passport_id'],
            'source_airport' => $sourceAirportId, // Store ID instead of code
            'destination_airport' => $destinationAirportId, // Store ID instead of code
            'departure_time' => $validated['departure_time'],
            'aircraft_number' => $validated['aircraft_number'],
            'seat' => $seat,
            'status' => 'booked',
        ]);

        return response()->json($ticket, 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/tickets/{id}/cancel",
     *     summary="Cancel a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Ticket ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket successfully cancelled",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ticket cancelled"),
     *             @OA\Property(property="ticket", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="passport_id", type="string", example="A1234567"),
     *                 @OA\Property(property="source_airport", type="integer", example=1),
     *                 @OA\Property(property="destination_airport", type="integer", example=2),
     *                 @OA\Property(property="departure_time", type="string", format="date-time", example="2025-03-10T12:00:00"),
     *                 @OA\Property(property="aircraft_number", type="string", example="AA101"),
     *                 @OA\Property(property="seat", type="string", example="B12"),
     *                 @OA\Property(property="status", type="string", example="cancelled"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-10T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-11T15:45:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ticket not found")
     *         )
     *     )
     * )
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->update(['status' => 'cancelled']);

            return response()->json(['message' => 'Ticket cancelled', 'ticket' => $ticket], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/tickets/{id}/seat",
     *     summary="Change seat of a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Ticket ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Seat successfully changed",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="passport_id", type="string", example="A1234567"),
     *             @OA\Property(property="source_airport", type="integer", example=1),
     *             @OA\Property(property="destination_airport", type="integer", example=2),
     *             @OA\Property(property="departure_time", type="string", format="date-time", example="2025-03-10T12:00:00"),
     *             @OA\Property(property="aircraft_number", type="string", example="AA101"),
     *             @OA\Property(property="seat", type="string", example="C15"),
     *             @OA\Property(property="status", type="string", example="booked"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-10T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-11T15:45:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot change seat of a cancelled ticket",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Cannot change seat of a cancelled ticket")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ticket not found")
     *         )
     *     )
     * )
     */
    public function changeSeat(int $id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($id);

            if ($ticket->status === 'cancelled') {
                return response()->json(['error' => 'Cannot change seat of a cancelled ticket'], 400);
            }

            do {
                $newSeat = chr(rand(65, 68)) . rand(1, 32);
            } while (Ticket::where('aircraft_number', $ticket->aircraft_number)
                ->where('departure_time', $ticket->departure_time)
                ->where('seat', $newSeat)
                ->exists());

            $ticket->update(['seat' => $newSeat]);

            return response()->json($ticket);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
    }
}
