<?php

namespace App\Utils;

use App\Mail\AdministratorMail;
use App\Mail\ClientEmail;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class Util
{
    public static function blockTransaction($transcationID)
    {
        $transaction = Transaction::find($transcationID);
        $transaction->block = true;
        $transaction->save();
    }

    public static function sendEmailToAdministrator($email)
    {
        // Get 48 hours latest transactions
        $date = Carbon::now()->subHours(48);
        $transactions = Transaction::where('created_at', '>=', $date)->get();
        // Data for mailable class
        $details = [
            'client' => 'Client',
            'transactions' => $transactions
        ];
        Mail::to($email)->send(new AdministratorMail($details));
    }

    public static function sendEmailToClient($email)
    {
        $details = [
            'client' => 'Client'
        ];
        Mail::to($email)->send(new ClientEmail($details));
    }
}
