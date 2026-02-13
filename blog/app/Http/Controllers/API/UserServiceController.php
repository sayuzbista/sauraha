<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorService;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class UserServiceController extends Controller
{
    public function allAvailableServices(Request $request)
    {
        $query = VendorService::with('vendor')
            ->where('status', 'approved')
            ->where('availability', 'Available'); // only approved services

        // Promotion priority: premium → gold → silver → others
        $query->orderByRaw("
            CASE 
                WHEN promotion_category = 'premium' THEN 1
                WHEN promotion_category = 'gold' THEN 2
                WHEN promotion_category = 'silver' THEN 3
                ELSE 4
            END
        ");

        // Only allow sorting by price
        if ($request->has('sort') && in_array($request->get('sort'), ['min_price', 'max_price'])) {
            if ($request->sort === 'min_price') {
                $query->orderBy('price', 'asc');
            } else if ($request->sort === 'max_price') {
                $query->orderBy('price', 'desc');
            }
        }

        $services = $query->get()->map(function ($service) {
            $service->images = $service->images
                ? array_map(fn($img) => url($img), $service->images)
                : [];
            return $service;
        });

        return response()->json($services);
    }

    public function serviceDetails($id)
    {
        $service = VendorService::with('vendor')->findOrFail($id);

        $service->images = $service->images
            ? array_map(fn($img) => url($img), $service->images)
            : [];

        return response()->json($service);
    }

    public function bookService(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:vendor_services,id',
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'persons' => 'required|integer|min:1'
        ]);

        $service = VendorService::findOrFail($request->service_id);

        $total_units = (int) $service->title; // title = total units

        $booked_units = Booking::where('vendor_service_id', $service->id)
            ->where('booking_date', $request->date)
            ->where('time_slot', $request->time_slot)
            ->sum('booked_units');

        $remaining_units = $total_units - $booked_units;

        if ($remaining_units <= 0 || $request->persons > $service->maxPersons) {
            return response()->json(['error' => 'Not enough availability'], 422);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'vendor_service_id' => $service->id,
            'booked_units' => 1,
            'persons' => $request->persons,
            'booking_date' => $request->date,
            'time_slot' => $request->time_slot
        ]);

        return response()->json(['success' => true, 'booking' => $booking]);
    }
}
