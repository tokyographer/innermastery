<?php
namespace Modules\User\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Booking\Models\Payment;
use Modules\User\Events\UpdateCreditPurchase;
use Modules\User\Models\Wallet\DepositPayment;
use Modules\User\Exports\WalletExport;

class WalletController extends AdminController
{
    public function addCredit($user_id = ''){
        if(empty($user_id)){
            abort(404);
        }
        $row = User::find($user_id);
        if(!$row){
            abort(404);
        }
        $data = [
            'row'=>$row,
            'page_title'=>__("Add Credit"),
            'breadcrumbs'=>[
                [
                    'url'=>route('user.admin.index'),
                    'name'=>__("Users"),
                ],
                [
                    'url'=>'#',
                    'name'=>__('Add credit for :name',['name'=>$row->display_name]),
                ],
            ]
        ];
        return view("User::admin.wallet.add-credit",$data);
    }
    public function removeCredit($user_id = ''){
        if(empty($user_id)){
            abort(404);
        }
        $row = User::find($user_id);
        if(!$row){
            abort(404);
        }

        $data = [
            'row'=>$row,
            'page_title'=>__("Remove Credit"),
            'breadcrumbs'=>[
                [
                    'url'=>route('user.admin.index'),
                    'name'=>__("Users"),
                ],
                [
                    'url'=>'#',
                    'name'=>__('Remove credit for :name',['name'=>$row->display_name]),
                ],
            ]
        ];
        return view("User::admin.wallet.remove-credit",$data);
    }
    public function store($user_id = ''){
        if(empty($user_id)){
            abort(404);
        }
        $row = User::find($user_id);
        if(!$row){
            abort(404);
        }
        $amount = request()->input('credit_amount',0);

        if($amount){
            try {
                $row->deposit($amount,[
                    'admin_deposit'=>auth()->id(),
                    'method_payment'=>request()->input('method_payment', null),
                    'concept'=>request()->input('concept',null)
                ]);

                $payment = new DepositPayment();
                $payment->payment_gateway = request()->input('method_payment', null);
                $payment->amount = $amount;
                $payment->status = "completed";
                $payment->create_user = auth()->id();
                $payment->object_id = $user_id;
                $payment->object_model = "wallet_deposit";
                $deposit_option = [
                    "name" => "No Bonus",
                    "amount" => $amount,
                    "credit" => $amount
                ];
                $payment->meta = json_encode([
                    'credit'=>$amount,
                    'deposit_option'=>$deposit_option
                ]);
                $payment->save();
            }catch (\Exception $exception){
                return redirect()->back()->with("error", $exception->getMessage());
            }

            return redirect()->back()->with("success",__(":amount credit added",['amount'=>$amount]));
        }
    }
    public function removestore($user_id = ''){
        if(empty($user_id)) abort(404);

        $row = User::find($user_id);

        if(!$row) abort(404);

        $amount = request()->input('credit_amount',0);

        $amount = - $amount;

        if($amount){
            try {
                $row->deposit($amount,[
                    'admin_deposit'=>auth()->id(),
                    'method_payment'=>request()->input('method_payment', null),
                    'concept'=>request()->input('concept',null)
                ]);
            }catch (\Exception $exception){
                return redirect()->back()->with("error",$exception->getMessage());
            }

            return redirect()->back()->with("success",__(":amount credit added",['amount'=>$amount]));
        }
    }
    public function report(){
        $query = DepositPayment::with('transfer_coin');

        $query->where('object_model','wallet_deposit')->orderBy('bravo_booking_payments.id','desc');
        if($user_id = request()->query('user_id'))
        {
            $query->where('object_id',$user_id);
        }
        if($status = request()->query('status'))
        {
            $query->where('status',$status);
        }

        if($agent_id = request()->query('agent_id'))
        {
            $query->join('users',"users.id", "bravo_booking_payments.object_id");
            $query->where('users.agent_id', $agent_id);
        }

        if(request()->query('from') && request()->query('to')){
            $from = request()->query('from');
            $to = request()->query('to');
            $query->whereBetween('bravo_booking_payments.updated_at', [$from, $to]);
        }

        $data = [
            'rows'=>$query->paginate(20),
            'page_title'=>__("Credit purchase report"),
            'breadcrumbs'=>[
                [
                    'url'=>route('user.admin.index'),
                    'name'=>__("Users"),
                ],
                [
                    'url'=>'#',
                    'name'=>__('Credit purchase report'),
                ],
            ]
        ];
        return view("User::admin.wallet.report",$data);
    }
    public function reportBulkEdit(Request $request){
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at lease 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));
        if ($action == 'delete') {
        //            foreach ($ids as $id) {
        //                if($id == Auth::id()) continue;
        //                $query = User::where("id", $id)->first();
        //                if(!empty($query)){
        //                    $query->email.='_d';
        //                    $query->save();
        //                    $query->delete();
        //                }
        //            }
        } else {
            foreach ($ids as $id) {
                switch ($action){
                    case "completed":
                        $payment = DepositPayment::find($id);
                        if($payment->payment_gateway == 'offline_payment' and $payment->status == 'processing'){
                            $payment->markAsCompleted();
                            //$payment->sendUpdatedPurchaseEmail();
                        }
                        event(new UpdateCreditPurchase(Auth::user(), $payment));

                        break;
                }
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }
    public function export(){
        return (new WalletExport())->download('wallet-' . date('M-d-Y') . '.xlsx');
    }
}
