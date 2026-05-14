<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', array_merge($this->dashboardPayload(), [
            'dashboardDataUrl' => route('panel.data.realtime'),
        ]));
    }
}
