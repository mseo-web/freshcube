<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryLog;

class AmoCRMController extends Controller
{
    public function index()
    {
        return inertia('AmoCRM/Home');
    }

    public function contactBinding()
    {
        return inertia('AmoCRM/ContactBinding');
    }

    public function history()
    {
        $history_logs = HistoryLog::all();
        // dd($history_logs);

        return inertia('AmoCRM/History', ['history_logs'=>$history_logs]);
    }
}
