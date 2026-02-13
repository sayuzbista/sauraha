<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    // Fetch all approved vendors for user panel
    public function approvedVendors()
    {
        $vendors = ServiceProvider::where('status', 'approved')
            ->select([
                'id',
                'company_name',
                'owner_name',
                'location',
                'email',
                'services_provided',
                'opening_time',
                'closing_time',
                'building_photo',
                'created_at',
            ])
            ->get();

        return response()->json($vendors);
    }
}
