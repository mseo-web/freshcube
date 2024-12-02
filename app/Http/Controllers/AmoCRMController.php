<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryLog;

use App\Http\Controllers\AmoCRMController;

class AmoCRMController extends Controller
{
    public function index()
    {
        $amoCRMIntegrity = new AmoCRMIntegrityController;

        $leadsList = $amoCRMIntegrity->leads_list();
        $leadsCollection = $leadsList->get();

        dd($leadsCollection);
        return inertia('AmoCRM/Home', ['leads_collection'=>$leadsCollection]);

        return inertia('AmoCRM/Home');
    }

    public function contactBinding($lead_id)
    {
        // dd($lead_id);
        return inertia('AmoCRM/ContactBinding', ['lead_id'=>$lead_id]);
    }

    public function contactBindingCreate(Request $request)
    {
        $lead_id = $request->lead_id;
        $name = $request->name;
        $phone = $request->phone;
        $comment = $request->comment;
        // ----------
        // Нужно Привязать контакт в amoCRM
        $result = 1; //от результата
        $data = ""; //от результата
        $data = json_encode($request->getContent(), JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT); //от результата
        // ----------
        $new_history_log = new HistoryLog;
        $new_history_log->action_date = now();
        $new_history_log->action = "Привязка контакта";
        $new_history_log->result = $result;
        $new_history_log->info = $data;
        $new_history_log->save();
        // ----------
        $history_logs = HistoryLog::all();
        // dd($history_logs);

        // return inertia('AmoCRM/History');
        return redirect('history');
    }

    public function history()
    {
        $history_logs = HistoryLog::all();
        // dd($history_logs);

        return inertia('AmoCRM/History', ['history_logs'=>$history_logs]);
    }
}
