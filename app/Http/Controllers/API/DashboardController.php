<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function datacount()
    {
        try {
            $userCount = User::count();

            return response()->json([
                'data' => [
                    'userCount' => $userCount
                ],
                'message' => 'Successfuly Fetching'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
