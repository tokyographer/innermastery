<?php
namespace Modules\User\Models\Wallet;

use App\User;
use Illuminate\Support\Facades\Mail;
use Modules\Booking\Models\Payment;
use Modules\User\Emails\CreditPaymentEmail;
use Modules\User\Models\UserTransferCoin;

class DepositPayment extends Payment
{
    public function user(){
        return $this->belongsTo(User::class,'object_id')->withDefault();
    }
    public static function countPending(){
        return parent::query()->where("object_model","wallet_deposit")->where("status",'processing')->count("id");
    }
    public function transfer_coin(){
        return $this->belongsTo(UserTransferCoin::class, 'transfer_coin_id');
    }
}
