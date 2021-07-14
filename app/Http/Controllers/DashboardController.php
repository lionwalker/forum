<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $counts = DB::table('posts')->select(DB::raw("COUNT(*) AS all_posts"),DB::raw("SUM(CASE WHEN approved=1 THEN 1 ELSE 0 END) AS approved_count"),DB::raw("SUM(CASE WHEN approved=0 THEN 1 ELSE 0 END) AS not_approved_count"))->get();
        return view('dashboard',compact('counts'));
    }
}
