<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Api\V1\DashboardStats;

class DashboardController extends Controller
{

    public function __construct(private DashboardStats $dashboardStats)
    {
    }

    public function index()
    {
        return response()->json($this->dashboardStats->getStats());
    }
}
