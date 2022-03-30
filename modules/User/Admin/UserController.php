<?php
namespace Modules\User\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\AdminController;
use Modules\User\Events\VendorApproved;
use Modules\User\Models\UserTransferCoin;
use Modules\Vendor\Models\VendorRequest;
use Modules\User\Models\Wallet\Transaction;
use Modules\User\Models\UserWallet;
use Modules\Booking\Models\Payment;
use Modules\User\Models\UserTransaction;
use Spatie\Permission\Models\Role;
use Modules\User\Exports\UserExport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
class UserController extends AdminController
{
    public function __construct()
    {
        $this->setActiveMenu('admin/module/user');
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->checkPermission('user_view');
        $username = $request->query('s');
        $listUser = User::with('agent')->orderBy('id','desc');

        $asesor=false;
        if(Auth::user()->roles[0]->name == "Asesor") $asesor=true;

        if($asesor) $listUser->where("agent_id", Auth::user()->id);

        if (!empty($username)) {
             $listUser->where(function($query) use($username){
                 $query->where('first_name', 'LIKE', '%' . $username . '%');
                 $query->orWhere('id',  $username);
                 $query->orWhere('phone',  $username);
                 $query->orWhere('email', 'LIKE', '%' . $username . '%');
                 $query->orWhere('last_name', 'LIKE', '%' . $username . '%');
             });
        }
        if($request->query('agent_id')){
            $listUser->where('agent_id', $request->query('agent_id'));
        }
        if($request->query('location_id')){
            $listUser->where('location_id', $request->query('location_id'));
        }
        if($request->query('role')){
            $listUser->role($request->query('role'));
        }
        $listUser->with(['wallet']);
        $data = [
            'rows' => $listUser->paginate(20),
            'roles' => Role::all()
        ];
        return view('User::admin.index', $data);
    }

    public function create(Request $request)
    {
        $user_end = User::orderBy('id', 'desc')->first()->id;
        $user_end = ($user_end + 1);
        $row = new \Modules\User\Models\User();
        $data = [
            'row' => $row,
            'roles' => Role::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("Users"),
                    'url'=>'admin/module/user'
                ]
            ],
            'user_end' => $user_end
        ];
        return view('User::admin.detail', $data);
    }

    public function edit(Request $request, $id)
    {
        $row = User::find($id);
        if (empty($row)) {
            return redirect('admin/module/user');
        }
        if ($row->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            abort(403);
        }
        $data = [
            'row'   => $row,
            'roles' => Role::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("Users"),
                    'url'=>'admin/module/user'
                ],
                [
                    'name'=>__("Edit User: #:id",['id'=>$row->id]),
                    'class' => 'active'
                ],
            ]
        ];
        return view('User::admin.detail', $data);
    }

    public function password(Request $request,$id){

        $row = User::find($id);
        $data  = [
            'row'=>$row,
            'currentUser'=>Auth::user()
        ];
        if (empty($row)) {
            return redirect('admin/module/user');
        }
        if ($row->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            abort(403);
        }
        return view('User::admin.password',$data);
    }

    public function changepass(Request $request, $id)
    {
        if(is_demo_mode()){
            return redirect()->back()->with("error", __("DEMO MODE: You can not change password!"));
        }
        $rules = [];
        $urow = User::find($id);
        if ($urow->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            abort(403);
        }
        $request->validate([
            'password'              => 'required|min:6|max:255',
            'password_confirmation' => 'required',
        ]);
        $password_confirmation = $request->input('password_confirmation');
        $password = $request->input('password');
        if ($password != $password_confirmation) {
            return redirect()->back()->with("error", __("Your New password does not matches. Please type again!"));
        }
        if ($urow->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            if ($password) {
                if ($urow->id != Auth::user()->id) {
                    $rules['old_password'] = 'required';
                }
                $rules['password'] = 'required|string|min:6|confirmed';
            }
            $this->validate($request, $rules);
            if ($password) {
                if (!(Hash::check($request->input('old_password'), $urow->password))) {
                    // The Old passwords matches
                    return redirect()->back()->with("error", __("Your current password does not matches with the password you provided. Please try again."));
                }
            }
        }
        $urow->password = bcrypt($password);
        if ($urow->save()) {

            if ($request->input('role_id') and $role = Role::findById($request->input('role_id'))) {
                $urow->assignRole($role);
            }
            return redirect()->back()->with('success', __('Password updated!'));
        }
    }

    public function store(Request $request, $id)
    {
        if($id and $id>0){
            $this->checkPermission('user_update');
            $row = User::find($id);
            if(empty($row)){
                abort(404);
            }
            if ($row->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
                abort(403);
            }

            $request->validate([
                'first_name'              => 'required|max:255',
                'last_name'              => 'required|max:255',
                'status'              => 'required|max:50',
                'phone'              => 'required',
                'country'              => 'required',
                'role_id'              => 'required|max:11',
                'email'              =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($row->id)
                ],
                'user_name'=> [
                    'required',
                    'max:255',
                    'min:4',
                    'string',
                    'alpha_dash',
                    Rule::unique('users')->ignore($row->id)
                ],
            ]);

        }else{
            $this->checkPermission('user_create');
            $check = Validator::make($request->input(),[
                'first_name'              => 'required|max:255',
                'last_name'              => 'required|max:255',
                'status'              => 'required|max:50',
                'phone'              => 'required',
                'country'              => 'required',
                'role_id'              => 'required|max:11',
                'email'              =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')
                ],
                'user_name'=> [
                    'required',
                    'max:255',
                    'min:4',
                    'string',
                    'alpha_dash',
                    Rule::unique('users')
                ],
            ]);

            if(!$check->validated()){
                return back()->withInput($request->input());
            }

            $row = new User();
            $row->email = $request->input('email');
        }

        $row->name = $request->input('name');
        $row->user_name = $request->input('user_name');
        $row->first_name = $request->input('first_name');
        $row->last_name = $request->input('last_name');
        $row->phone = $request->input('phone');
        $row->birthday = date("Y-m-d", strtotime($request->input('birthday')));
        $row->address = $request->input('address');
        $row->address2 = $request->input('address2');
        $row->bio = clean($request->input('bio'));
        $row->status = $request->input('status');
        $row->avatar_id = $request->input('avatar_id');
        $row->email = $request->input('email');
        $row->country = $request->input('country');
        $row->city = $request->input('city');
        $row->state = $request->input('state');
        $row->zip_code = $request->input('zip_code');
        $row->business_name = $request->input('business_name');
        $row->vendor_commission_type = $request->input('vendor_commission_type');
        $row->vendor_commission_amount = $request->input('vendor_commission_amount');
        $row->agent_id = $request->input('agent_id') ?? null;
        $row->customer = $request->input('customer') ?? null;
        $row->location_id = $request->input('location_id') ?? null;
        //Block all service when user is block
        if($row->status == "blocked"){
            $services = get_bookable_services();
            if(!empty($services)){
                foreach ($services as $service){
                    $service::query()->where("create_user",$row->id)->update(['status' => "draft"]);
                }
            }
        }

        if ($row->save()) {

            if ($request->input('role_id') and $role = Role::findById($request->input('role_id'))) {
                $row->syncRoles($role);
            }
            return back()->with('success', ($id and $id>0) ? __('User updated'):__("User created"));
        }
    }

    public function getForSelect2(Request $request)
    {
        $q = $request->query('q');
        $query = User::select('*');
        if ($q) {
            $query->where(function ($query) use ($q) {
                $query->where('first_name', 'like', '%' . $q . '%')->orWhere('last_name', 'like', '%' . $q . '%')->orWhere('email', 'like', '%' . $q . '%')->orWhere('id', $q)->orWhere('phone', 'like', '%' . $q . '%');
            });
        }
        $res = $query->orderBy('id', 'desc')->orderBy('first_name', 'asc')->limit(20)->get();
        $data = [];
        if (!empty($res)) {
            if($request->query("user_type") == "vendor"){
                //for only vendor
                foreach ($res as $item) {
                    if($item->hasPermissionTo("dashboard_vendor_access")){
                        $data[] = [
                            'id'   => $item->id,
                            'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (#' . $item->id . ')' : $item->email . ' (#' . $item->id . ')',
                        ];
                    }
                }
            }else{
                //for all
                foreach ($res as $item) {
                    $data[] = [
                        'id'   => $item->id,
                        'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (#' . $item->id . ')' : $item->email . ' (#' . $item->id . ')',
                    ];
                }
            }
        }
        return response()->json([
            'results' => $data
        ]);
    }

    public function getForAgent(Request $request)
    {
        $q = $request->query('q');
        $query = User::select('*');
        if ($q) {
            $query->where(function ($query) use ($q) {
                $query->where('first_name', 'like', '%' . $q . '%')->orWhere('last_name', 'like', '%' . $q . '%')->orWhere('email', 'like', '%' . $q . '%')->orWhere('id', $q)->orWhere('phone', 'like', '%' . $q . '%');
            });
        }
        $res = $query->orderBy('id', 'desc')->orderBy('first_name', 'asc')->limit(20)->get();
        $data = [];
        if (!empty($res)) {
            foreach ($res as $item) {
                $data[] = [
                    'id'   => $item->id,
                    'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (' . $item->email . ')' : $item->email . ' (#' . $item->id . ')',
                ];
            }
        }
        return response()->json([
            'results' => $data
        ]);
    }

    public function getForAsesor(Request $request)
    {
        $listUser = User::query()->orderBy('id','desc');
        $listUser->role("Asesor");
        $res = $listUser->orderBy('id', 'desc')->orderBy('first_name', 'asc')->limit(20)->get();

        $data = [];
        if (!empty($res)) {
            foreach ($res as $item) {
                $data[] = [
                    'id'   => $item->id,
                    'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (' . $item->email . ')' : $item->email . ' (#' . $item->id . ')',
                ];
            }
        }
        return response()->json([
            'results' => $data
        ]);
    }

    public function getForCustomer(Request $request)
    {
        $listUser = User::query()->orderBy('id','desc');
        $listUser->where("customer", 1);
        $res = $listUser->orderBy('id', 'desc')->orderBy('first_name', 'asc')->limit(20)->get();

        $data = [];
        if (!empty($res)) {
            foreach ($res as $item) {
                $data[] = [
                    'id'   => $item->id,
                    'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (' . $item->email . ')' : $item->email . ' (#' . $item->id . ')',
                ];
            }
        }
        return response()->json([
            'results' => $data
        ]);
    }


    public function bulkEdit(Request $request)
    {
        if(is_demo_mode()){
            return redirect()->back()->with("error","DEMO MODE: You are not allowed to do it");
        }
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));
        if ($action == 'delete') {
            foreach ($ids as $id) {
                if($id == Auth::id()) continue;
                $query = User::where("id", $id)->first();
                if(!empty($query)){
                    $query->email.='_d_'.uniqid().rand(0,99999);
                    $query->save();
                    $query->delete();
                }
            }
        } else {
            foreach ($ids as $id) {
                User::where("id", $id)->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }
    public function userUpgradeRequest(Request $request)
    {
        $this->checkPermission('user_view');
        $listUser = VendorRequest::query();
        $data = [
            'rows' => $listUser->with(['user','role','approvedBy'])->orderBy('id','desc')->paginate(20),
            'roles' => Role::all(),

        ];
        return view('User::admin.upgrade-user', $data);
    }
    public function userUpgradeRequestApproved(Request $request)
    {
        $this->checkPermission('user_create');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at leas 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));

        switch ($action){
            case "delete":
                foreach ($ids as $id) {
                    $query = VendorRequest::find( $id);
                    if(!empty($query)){
                        $query->delete();
                    }
                }
                return redirect()->back()->with('success', __('Deleted success!'));
                break;
            default:
                foreach ($ids as $id) {
                    $vendorRequest = VendorRequest::find( $id);
                    if(!empty($vendorRequest)){
                        $vendorRequest->update(['status' => $action,'approved_time'=>now(),'approved_by'=>Auth::id()]);
                        $user = User::find($vendorRequest->user_id);
                        if(!empty($user)){
                            $user->syncRoles($vendorRequest->role_request);
                        }
                        event(new VendorApproved($user,$vendorRequest));
                    }
                }
                return redirect()->back()->with('success', __('Updated successfully!'));
                break;
        }
    }
    public function userUpgradeRequestApprovedId(Request $request, $id)
    {
        $this->checkPermission('user_create');
        if (empty($id))
            return redirect()->back()->with('error', __('Select at least 1 item!'));

        $vendorRequest = VendorRequest::find( $id);
        if(!empty($vendorRequest)){
            $vendorRequest->update(['status' => 'approved','approved_time'=>now(),'approved_by'=>Auth::id()]);
            $user = User::find($vendorRequest->user_id);
            if(!empty($user)){
                $user->syncRoles($vendorRequest->role_request);
            }

            event(new VendorApproved($user,$vendorRequest));
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

    public function export()
    {
        return (new UserExport())->download('user-' . date('M-d-Y') . '.xlsx');
    }
    public function verifyEmail(Request $request,$id)
    {
        $user = User::find($id);
        if(!empty($user)){
            $user->email_verified_at = now();
            $user->save();
            return redirect()->back()->with('success', __('Verify email successfully!'));
        }else{
            return redirect()->back()->with('error', __('Verify email cancel!'));
        }
    }


    public function transfer_list(){
        $row = auth()->user();
        $rows = UserTransferCoin::with("from", "to")->where("from_id", $row->id);
        if(request()->from){
            $rows->where("from_id", request()->from);
        }
        if(request()->to){
            $rows->where("to_id", request()->to);
        }
        $data = [
            'rows'        => $rows->paginate(20),
            'breadcrumbs' => [
                [
                    'name'  => __('List Transfer'),
                    'class' => 'active'
                ],
            ],
            'page_title'  => __("List Transfer"),
        ];
        return view('User::admin.transfer.list', $data);
    }

    public function transfer(){
        $row = auth()->user();
        $data = [
            'row'=>$row,
            'breadcrumbs' => [
                [
                    'name'  => __('Balance Transfer'),
                    'class' => 'active'
                ],
            ],
            'page_title'  => __("Balance Transfer"),
        ];
        return view('User::admin.transfer.transfer', $data);
    }

    public function transfer_store(Request $request){
        $email = $request->input("email");
        $price = $request->input("price");
        $payment_concept = $request->input("payment_concept");

        if($email && $price){
            $user = User::find($email);
            $row = auth()->user();
            $uuid = Str::uuid();
            $price = intval($price);

            DB::update("UPDATE user_wallets SET balance = balance + $price WHERE holder_id = $user->id");
            DB::update("UPDATE user_wallets SET balance = balance - $price WHERE holder_id = $row->id");

            $transfer_coin = new UserTransferCoin();
            $transfer_coin->from_id = $row->id;
            $transfer_coin->to_id = $user->id;
            $transfer_coin->status = 'completed';
            $transfer_coin->uuid = $uuid;
            $transfer_coin->coins = $price;
            $transfer_coin->payment_concept = $payment_concept;
            $transfer_coin->save();


            $uuid = Str::uuid();
            $user_to = UserWallet::where('holder_id', $user->id)->first();
            if($user_to){
                $transaction = new UserTransaction();
                $transaction->payable_type = "App\User";
                $transaction->payable_id = $user->id;
                $transaction->wallet_id = $user_to->id;
                $transaction->type = "deposit";
                $transaction->amount = $price;
                $transaction->confirmed = 1;
                $transaction->meta = json_encode([
                    'admin_deposit'=> $row->id,
                    'method_payment'=> "deposit",
                    "concept"=> $payment_concept
                ]);
                $transaction->uuid = $uuid;
                $transaction->create_user = $row->id;
                $transaction->save();


                $code = md5(uniqid() . rand(0, 99999));
                $payment = new Payment();
                $payment->booking_id = null;
                $payment->payment_gateway = "deposit";
                $payment->amount = $price;
                $payment->status = "completed";
                $payment->logs = json_encode([
                    'admin_deposit'=> $row->id,
                    'method_payment'=> "deposit",
                    "concept"=> $payment_concept
                ]);
                $payment->create_user = $row->id;
                $payment->update_user = $row->id;
                $payment->code = $code;
                $payment->object_id = $user_to->id;
                $payment->object_model = "wallet_deposit";
                $payment->meta = json_encode([
                    'credit'=> $price,
                    'deposit_option' => [
                        'name' => 'No Bonus',
                        'amount' => $price,
                        'credit' => $price ,
                    ]
                ]);
                $payment->wallet_transaction_id = $transaction->id;
                $payment->transfer_coin_id = $transfer_coin->id;
                $payment->save();
            }

            $uuid = Str::uuid();
            $user_from = UserWallet::where('holder_id', $row->id)->first();
            if($user_from){
                $transaction = new UserTransaction();
                $transaction->payable_type = "App\User";
                $transaction->payable_id = $row->id;
                $transaction->wallet_id = $user_from->id;
                $transaction->type = "withdraw";
                $transaction->amount = $price;
                $transaction->confirmed = 1;
                $transaction->meta = json_encode([
                    'admin_deposit'=> $row->id,
                    'method_payment'=> "withdraw",
                    "concept"=> $payment_concept
                ]);
                $transaction->uuid = $uuid;
                $transaction->create_user = $row->id;
                $transaction->save();


                $code = md5(uniqid() . rand(0, 99999));
                $payment = new Payment();
                $payment->booking_id = null;
                $payment->payment_gateway = "withdraw";
                $payment->amount = $price;
                $payment->status = "completed";
                $payment->logs = json_encode([
                    'admin_deposit'=> $row->id,
                    'method_payment'=> "withdraw",
                    "concept"=> $payment_concept
                ]);
                $payment->create_user = $row->id;
                $payment->update_user = $row->id;
                $payment->code = $code;
                $payment->object_id = $row->id;
                $payment->object_model = "wallet_deposit";
                $payment->meta = json_encode([
                    'credit'=> $price,
                    'deposit_option' => [
                        'name' => 'No Bonus',
                        'amount' => $price,
                        'credit' => $price ,
                    ]
                ]);
                $payment->wallet_transaction_id = $transaction->id;
                $payment->transfer_coin_id = $transfer_coin->id;
                $payment->save();
            }

            return redirect()->back()->with('success', __('Transfer completed'));
        }
        return redirect()->back()->with('error', __('Transfer Not completed'));
    }

    public function user_view($id){
        $row = User::with('agent', 'location')->whereRaw("md5(id) = ?", $id)->first();
        $data = [
            'row'   => $row,
            'roles' => Role::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("Users"),
                    'url'=>'admin/module/user'
                ],
                [
                    'name'=>__("View User: #:id",['id'=>$row->id]),
                    'class' => 'active'
                ],
            ]
        ];
        return view('User::admin.view_data', $data);
    }
}
