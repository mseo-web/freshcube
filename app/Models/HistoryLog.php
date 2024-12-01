<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    protected $table = "history_logs";

    protected $fillable = [
        'action_date',
        'action',
        'result',
        'info',
    ];
}
