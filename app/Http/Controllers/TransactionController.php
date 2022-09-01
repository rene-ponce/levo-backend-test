<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AttemptHistory;
use App\Models\Rejection;
use App\Models\Transaction;
use App\Utils\Util;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check headers
        if (!$request->header('clientID')) {
            return response()->json([
                'error' => true,
                'statusCode' => 400,
                'message' => 'Client ID not found in headers request',
                'data' => null
            ]);
        }
        // Get tables information
        $account = Account::find($request->account_id);
        $history = AttemptHistory::where('client_id', $request->client_id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Save transaction
        $transaction = new Transaction();
        $transaction->client_id = $request->header('clientID');
        $transaction->account_id = $request->account_id;
        $transaction->amount = $request->amount;
        $transaction->transaction_type = $request->transaction_type;
        $transaction->save();

        // Rules
        if ($request->amount > $account->balance) {
            // check attempts
            if ($history->attempts >= 3) {
                Util::sendEmailToClient('client@mailinator.com');
            }
        }
        if ($request->amount > env('AMOUNT_ALLOWED')) {
            // Block transaction
            Util::blockTransaction($transaction->id);
            // Save rejection
            $rejection = new Rejection();
            $rejection->transaction_id = $transaction->id;
            $rejection->reason = 'Rejection for amount greater than allowed';
            $rejection->save();
            // Send administrator notification
            Util::sendEmailToAdministrator('admin@mailinator.com');
        }
        // Apply transaction in account
        if (!$transaction->block) {
            $account->balance = ($request->transaction_type === 'deposit') ? $account->balance += $request->amount : $account->balance -= $request->amount;
            $account->save();
        }
        return response()->json([
            'transaction' => $transaction
        ]);
    }
}
