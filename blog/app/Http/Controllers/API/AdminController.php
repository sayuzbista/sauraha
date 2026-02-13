<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider; 
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\VendorService;
use App\Mail\VendorApprovedMail;
use App\Mail\VendorRejectedMail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    // Hardcoded admin credentials
    private $adminUsername = 'sayuzbista46@gmail.com';
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
public function approve($id)
{

    $vendor = ServiceProvider::findOrFail($id);

    // Change status
    $vendor->status = 'approved';
    $vendor->save();

    // Send approval email
    Mail::to($vendor->email)->send(new VendorApprovedMail($vendor));

    return response()->json([
        'message' => 'Vendor approved and email sent successfully.'
    ]);
}

public function reject($id)
{
    $vendor = ServiceProvider::findOrFail($id);

    // Update status
    $vendor->status = 'rejected';
    $vendor->save();

    // Send rejection email
    Mail::to($vendor->email)->send(new VendorRejectedMail($vendor));

    return response()->json([
        'message' => 'Vendor rejected and email sent successfully.'
    ]);
}
public function approvedVendors()
{
    $vendors = ServiceProvider::where('status', 'approved')->get();
    return response()->json($vendors);
}
public function rejectedVendors()
{
    $vendors = ServiceProvider::where('status', 'rejected')->get();
    return response()->json($vendors);
}

public function deleteVendor($id)
{
    $vendor = ServiceProvider::find($id);

    if (!$vendor) {
        return response()->json(['message' => 'Vendor not found.'], 404);
    }

    // Delete the vendor record from the database
    $vendor->delete();

    return response()->json(['message' => 'Vendor deleted successfully.']);
}


    // Fetch pending vendor services with vendor info
    public function pendingServices()
    {
        $services = VendorService::with('vendor')  // assumes VendorService has 'vendor()' relation
            ->where('status', 'pending')
            ->get();

        return response()->json($services);
    }

    // Approve service
   public function approveService($id)
{
    $service = VendorService::with('vendor')->findOrFail($id);

    $service->status = 'approved';
    $service->save();

    if ($service->vendor) {
        $vendorEmail = $service->vendor->email;
        $companyName = $service->vendor->company_name;

        Mail::raw(
            "Dear $companyName,\n\n".
            "We are pleased to inform you that your service titled '{$service->category}' has been successfully approved and is now live on our platform.\n\n".
            "Important Notice:\n".
            "- If you update your service visibility from Gold to Premium , Premium to Gold or from silver to premium/gold , your service will require admin verification again before it becomes active.\n".
            "- If you change your promotion category to Silver, no additional verification will be required.\n\n".
            "We appreciate your cooperation and commitment to maintaining high service standards.\n\n".
            "Best regards,\n".
            "Admin Team",
            function ($message) use ($vendorEmail) {
                $message->to($vendorEmail)
                        ->subject('Your Service Has Been Approved');
            }
        );
    }

    return response()->json([
        'message' => 'Service approved and email sent successfully',
        'service' => $service
    ]);
}


    // Reject service with reason
    public function rejectService(Request $request, $id)
{
    // Validate the reason
    $request->validate([
        'reason' => 'required|string|max:1000'
    ]);

    // Find the service
    $service = VendorService::with('vendor')->findOrFail($id); // make sure vendor is loaded

    // Update status only
    $service->status = 'rejected';
    $service->save();

    // Send rejection email to vendor
    if ($service->vendor) {
        $vendorEmail = $service->vendor->email;
        $vendorName = $service->vendor->company_name ?? $service->vendor->name;

        Mail::raw(
            "Hello $vendorName,\n\nYour service '{$service->title}' has been rejected by admin.\nReason: {$request->reason}\n\nPlease update your service and resubmit.",
            function ($message) use ($vendorEmail) {
                $message->to($vendorEmail)
                        ->subject('Your service has been rejected');
            }
        );
    }

    return response()->json([
        'message' => 'Service rejected and email sent',
        'service' => $service
    ]);
}


}
