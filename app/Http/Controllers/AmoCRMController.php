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

        $leads = $amoCRMIntegrity->leads_list();
        

        // dd($leads);
        return inertia('AmoCRM/Home', ['leads'=>$leads]);

        return inertia('AmoCRM/Home');
    }

    public function contactBinding($lead_id)
    {
        // dd($lead_id);
        return inertia('AmoCRM/ContactBinding', ['lead_id'=>$lead_id]);
    }

    public function contactBindingCreate(Request $request)
    {
        $amoCRMIntegrity = new AmoCRMIntegrityController;
        $lead_id = $request->lead_id;
        $name = $request->name;
        $phone = $request->phone;
        $comment = $request->comment;
        // ----------
        // Нужно Привязать контакт в amoCRM
        $result_response = $amoCRMIntegrity->add_contact_to_lead([
            'LEAD_ID' => $lead_id,
            'NAME' => $name,
            'PHONE' => $phone,
            'COMMENT' => $comment,
        ]);
        if($result_response=="result_succese") {
            $result = 1;
        }
        if($result_response=="result_error") {
            $result = 0;
        }

        if($result==1) {
            // $data = json_encode($request->getContent(), JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
            $data = "Создан контакт ".$name." и привязан к сделке ID - ".$lead_id;
        }
        if($result==0) {
            $data = "";
        }
        // ----------
        $new_history_log = new HistoryLog;
        $new_history_log->action_date = now();
        $new_history_log->action = "Привязка контакта";
        $new_history_log->result = $result;
        $new_history_log->info = $data;
        $new_history_log->save();
        // ----------
        // $history_logs = HistoryLog::all();
        // dd($history_logs);

        // return inertia('AmoCRM/History');
        return redirect('history');
    }

    public function history()
    {
        // $history_logs = HistoryLog::all();
        $history_logs = HistoryLog::orderByDesc('id')->get();
        // dd($history_logs);

        return inertia('AmoCRM/History', ['history_logs'=>$history_logs]);
    }
}
