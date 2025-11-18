<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class AdminController extends Controller
{
    // Hardcoded admin credentials
    private $adminUsername = '9861792189';
    private $adminPassword = 'sayuz123';

    // Admin login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($request->username === $this->adminUsername && $request->password === $this->adminPassword) {
            return response()->json([
                'message' => 'Admin login successful',
                'token' => 'admin-token-example' // simple token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Add a service
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;

        if ($request->hasFile('image')) {
            $service->image = $request->file('image')->store('services', 'public');
        }

        $service->save();

        return response()->json(['message' => 'Service added successfully', 'service' => $service]);
    }

    // Optional: get all services (for landing page)
    public function listServices()
    {
        $services = Service::all();
        return response()->json($services);
    }
    // Update service
// Update service
public function updateService(Request $request, $id)
{
    $service = Service::find($id);
    if (!$service) {
        return response()->json(['message' => 'Service not found'], 404);
    }

    $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'price' => 'nullable|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $service->name = $request->name;
    $service->description = $request->description;
    $service->price = $request->price;

    if ($request->hasFile('image')) {
        if ($service->image) {
            \Storage::disk('public')->delete($service->image); // delete old image
        }
        $service->image = $request->file('image')->store('services', 'public');
    }

    $service->save();
    return response()->json(['message' => 'Service updated successfully', 'service' => $service]);
}

// Delete service
public function deleteService($id)
{
    $service = Service::find($id);
    if (!$service) return response()->json(['message' => 'Service not found'], 404);

    if ($service->image) {
        \Storage::disk('public')->delete($service->image); // delete image
    }

    $service->delete();
    return response()->json(['message' => 'Service deleted successfully']);
}


}
