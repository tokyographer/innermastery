<?php
namespace Modules\User\Models;
use App\BaseModel;
use App\User;

class UserTransferCoin extends BaseModel
{
    protected $table = 'user_transfers_coins';

    public function from(){
        return $this->belongsTo(User::class, "from_id");
    }
    public function to(){
        return $this->belongsTo(User::class, "to_id");
    }
}
