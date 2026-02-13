<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Models\VendorService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; 
class ServiceProviderController extends Controller
{
    // Vendor Registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email',
            'phone_number' => 'required|string',
            'services_provided' => 'required|array|min:1',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'password' => 'required|string|confirmed|min:6',
            'pan_number' => 'required|string',
            'company_doc' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'building_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $companyDocPath = $request->file('company_doc')->store('vendors/docs', 'public');
        $buildingPhotoPath = $request->file('building_photo')->store('vendors/photos', 'public');

       try{ $vendor = ServiceProvider::create([
            'company_name' => $request->company_name,
            'owner_name' => $request->owner_name,
            'location' => $request->location,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'services_provided' => json_encode($request->services_provided),
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'password' => Hash::make($request->password),
            'pan_number' => $request->pan_number,
            'company_doc' => $companyDocPath,
            'building_photo' => $buildingPhotoPath,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Registration request sent. Await admin approval.your confirmation or rejection will be provided in your mail',
            'vendor' => $vendor
        ]);
    }catch (\Exception $e) {
    return response()->json([
        'message' => 'Server Error',
        'error' => $e->getMessage()
    ], 500);
}
    }

    // Admin: Approve Vendor
    

    // List Pending Vendors for Admin
    public function pendingVendors()
    {
        $vendors = ServiceProvider::where('status', 'pending')->get();
        return response()->json($vendors);
    }

  public function login(Request $request)
{
    // Validate input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Find vendor by email
    $vendor = ServiceProvider::where('email', $request->email)->first();

    if (!$vendor || !Hash::check($request->password, $vendor->password)) {
        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }
     if ($vendor->status !== 'approved') {
        return response()->json([
            'message' => 'Your account is not approved yet. Please wait for admin approval.'
        ], 403);
    }

    // Generate manual token and save in DB
    $token = Str::random(60);
    $vendor->api_token = $token;
    $vendor->save();

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'vendor' => $vendor
    ]);
}
   

    // Optional: Logout vendor
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
       public function dashboard(Request $request){
        return response()->json([
            'message' => 'Welcome to dashboard',
            'vendor' => $request->vendor
        ]);
    }

    // List all services for logged-in vendor
   public function listServices(Request $request)
    {
        $vendorId = $request->vendor->id;
        $services = VendorService::where('vendor_id', $vendorId)->get();
        return response()->json($services);
    }

    // ADD SERVICE
  // ADD SERVICE
// ADD SERVICE
public function addService(Request $request)
{
    $vendorId = $request->vendor->id;

    // Validate input
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',  // stores no of vehicles
        'category' => 'required|string|max:255',
        'price' => 'required|numeric',
        'duration_hours' => 'required|numeric|min:0.5',
        'maxPersons' => 'nullable|integer|min:1',
        'offer_percentage' => 'nullable|integer|min:0|max:100',
        'free_person_offer' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'availability' => 'required|string',
        'promotion_category' => 'required|string|in:Premium,Gold,Silver',
        'payment_screenshot' => 'required_if:promotion_category,Premium,Gold|nullable|string',
        'images.*' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $service = new VendorService();
    $service->vendor_id = $vendorId;
    $service->title = $request->title;
    $service->category = $request->category;
    $service->price = $request->price;
    $service->duration_hours = $request->duration_hours;
    $service->maxPersons = $request->maxPersons;
    $service->offer_percentage = $request->offer_percentage;
    $service->free_person_offer = $request->free_person_offer;
    $service->description = $request->description;
    $service->availability = $request->availability;
    $service->promotion_category = $request->promotion_category;
    $service->status = 'pending'; // default

    // -------- HANDLE SERVICE IMAGES --------
    $serviceImages = [];
    if ($request->images && is_array($request->images)) {
        $folder = public_path('vendor_services');
        if (!file_exists($folder)) mkdir($folder, 0755, true);

        foreach ($request->images as $index => $img) {
            try {
                $imageName = 'service_'.$vendorId.'_'.time().'_'.$index.'.png';
                $imagePath = $folder.'/'.$imageName;
                file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img)));
                $serviceImages[] = url('vendor_services/'.$imageName);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to save service image', 'error' => $e->getMessage()], 500);
            }
        }
    }
    $service->images = $serviceImages;

    // -------- HANDLE PAYMENT SCREENSHOT --------
    if (in_array($request->promotion_category, ['Premium', 'Gold']) && $request->payment_screenshot) {
        try {
            $folder = public_path('vendor_services/payment_screenshots');
            if (!file_exists($folder)) mkdir($folder, 0755, true);

            $paymentName = 'payment_'.$vendorId.'_'.time().'.png';
            $paymentPath = $folder.'/'.$paymentName;
            file_put_contents($paymentPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->payment_screenshot)));
            $service->payment_screenshot = url('vendor_services/payment_screenshots/'.$paymentName);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save payment screenshot', 'error' => $e->getMessage()], 500);
        }
    } else {
        $service->payment_screenshot = null;
    }

    $service->save();

    return response()->json(['message' => 'Service added successfully', 'service' => $service]);
}

// UPDATE SERVICE
public function updateService(Request $request, $id)
{
    $vendorId = $request->vendor->id;

    $service = VendorService::where('id', $id)
                            ->where('vendor_id', $vendorId)
                            ->firstOrFail();

    $previousPromotion = $service->promotion_category; // old category

    // Validate input
    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|required|string|max:255', // number of vehicles as string
        'category' => 'sometimes|required|string|max:255',
        'price' => 'sometimes|required|numeric',
        'duration_hours' => 'sometimes|required|numeric|min:0.5',
        'maxPersons' => 'nullable|integer|min:1',
        'offer_percentage' => 'nullable|integer|min:0|max:100',
        'free_person_offer' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'availability' => 'sometimes|required|string',
        'promotion_category' => 'sometimes|required|string|in:Premium,Gold,Silver',
        'payment_screenshot' => 'nullable|string',
        'images.*' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update fields
    $service->title = $request->title ?? $service->title;
    $service->category = $request->category ?? $service->category;
    $service->price = $request->price ?? $service->price;
    $service->duration_hours = $request->duration_hours ?? $service->duration_hours;
    $service->maxPersons = $request->maxPersons ?? $service->maxPersons;
    $service->offer_percentage = $request->offer_percentage ?? $service->offer_percentage;
    $service->free_person_offer = $request->free_person_offer ?? $service->free_person_offer;
    $service->description = $request->description ?? $service->description;
    $service->availability = $request->availability ?? $service->availability;

    // -------- UPDATE PROMOTION CATEGORY AND STATUS --------
    $newPromotion = $request->promotion_category ?? $service->promotion_category;

    // If changing to Premium/Gold from Silver or switching Gold<->Premium → status = pending
    if (
        ($previousPromotion === 'Silver' && in_array($newPromotion, ['Gold', 'Premium'])) ||
        (in_array($previousPromotion, ['Gold', 'Premium']) && in_array($newPromotion, ['Gold', 'Premium']) && $previousPromotion !== $newPromotion)
    ) {
        $service->status = 'pending'; // requires admin verification
    }
    // If changing from Gold/Premium → Silver, status can remain as is
    // Otherwise, keep previous status
    $service->promotion_category = $newPromotion;

    // -------- UPDATE SERVICE IMAGES --------
    if ($request->images && is_array($request->images) && count($request->images) > 0) {
        $serviceImages = [];
        $folder = public_path('vendor_services');
        if (!file_exists($folder)) mkdir($folder, 0755, true);

        foreach ($request->images as $index => $img) {
            try {
                $imageName = 'service_'.$vendorId.'_'.time().'_'.$index.'.png';
                $imagePath = $folder.'/'.$imageName;
                file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img)));
                $serviceImages[] = url('vendor_services/'.$imageName);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to save service image', 'error' => $e->getMessage()], 500);
            }
        }

        $service->images = $serviceImages;
    }

    // -------- UPDATE PAYMENT SCREENSHOT --------
    if (in_array($service->promotion_category, ['Premium', 'Gold']) && $request->payment_screenshot) {
        try {
            $folder = public_path('vendor_services/payment_screenshots');
            if (!file_exists($folder)) mkdir($folder, 0755, true);

            $paymentName = 'payment_'.$vendorId.'_'.time().'.png';
            $paymentPath = $folder.'/'.$paymentName;
            file_put_contents($paymentPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->payment_screenshot)));
            $service->payment_screenshot = url('vendor_services/payment_screenshots/'.$paymentName);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save payment screenshot', 'error' => $e->getMessage()], 500);
        }
    }

    $service->save();

    return response()->json(['message' => 'Service updated successfully', 'service' => $service]);
}


public function getService(Request $request, $id)
{
    $vendorId = $request->vendor->id;

    $service = VendorService::where('id', $id)
                            ->where('vendor_id', $vendorId)
                            ->first();

    if (!$service) {
        return response()->json(['message' => 'Service not found'], 404);
    }

    return response()->json($service);
}

    // DELETE SERVICE
    public function deleteService(Request $request, $id)
    {
        $vendorId = $request->vendor->id;
        $service = VendorService::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }


}
