<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardDataController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json($this->dashboardPayload());
    }
}