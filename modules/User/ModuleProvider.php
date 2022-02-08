<?php
namespace Modules\User;
use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\ModuleServiceProvider;
use Modules\Vendor\Models\VendorRequest;
use Spatie\Permission\Models\Role;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    public static function getAdminMenu()
    {
        $noti_verify = User::countVerifyRequest();
        $noti_upgrade = VendorRequest::where('status', 'pending')->count();
        $noti = $noti_verify;

        $options = [
            "position"=>2,
            'url'        => 'admin/module/user',
            'title'      => __('Users :count',['count'=>$noti ? sprintf('<span class="badge badge-warning">%d</span>',$noti) : '']),
            'icon'       => 'icon ion-ios-contacts',
            'permission' => 'user_view',
            'children'   => []
        ];

        $roles = Role::all();
        foreach($roles as $key => $role){
            $options['children']['user' . ($key + 1)] = [
                'url'   => 'admin/module/user?role=' . $role->name,
                'title' => __($role->name),
                'icon'  => 'fa fa-user',
            ];
        }

        $options['children']['role'] = [
            'url'        => 'admin/module/user/role',
            'title'      => __('Role Manager'),
            'permission' => 'role_view',
            'icon'       => 'fa fa-lock',
        ];
        $options['children']['subscriber'] = [
            'url'        => 'admin/module/user/subscriber',
            'title'      => __('Subscribers'),
            'permission' => 'newsletter_manage',
        ];
        $options['children']['userUpgradeRequest'] = [
            'url'        => 'admin/module/user/userUpgradeRequest',
            'title'      => __('Upgrade Request :count',['count'=>$noti_upgrade ? sprintf('<span class="badge badge-warning">%d</span>',$noti_upgrade) : '']),
            'permission' => 'user_view',
        ];

        $is_disable_verification_feature = setting_item('user_disable_verification_feature');
        if(empty($is_disable_verification_feature)){
            $options['children']['user_verification'] = [
                'url'        => 'admin/module/user/verification',
                'title'      => __('Verification Request :count',['count'=>$noti_verify ? sprintf('<span class="badge badge-warning">%d</span>',$noti_verify) : '']),
                'permission' => 'user_view',
            ];
        }

        return [
            'users'=> $options
        ];
    }
    public static function getUserMenu()
    {
        /**
         * @var $user User
         */
        $res = [];
        $user = Auth::user();

        $is_wallet_module_disable = setting_item('wallet_module_disable');
        if(empty($is_wallet_module_disable))
        {
            $res['wallet']= [
                'position'   => 27,
                'icon'       => 'fa fa-money',
                'url'        => route('user.wallet'),
                'title'      => __("My Wallet"),
            ];
        }

        $is_disable_verification_feature = setting_item('user_disable_verification_feature');
        if(!empty($user->verification_fields) and empty($is_disable_verification_feature))
        {
            $res['verification']= [
                'url'        => route('user.verification.index'),
                'title'      => __("Verifications"),
                'icon'       => 'fa fa-handshake-o',
                'position'   => 39,
            ];
        }

        $res['exchange']= [
            'icon'       => 'icofont-ebook',
            'url'        => route('vendor.enquiry_report'),
            'title'      => __("Enquiry Report"),
            'permission' => 40,
        ];

        $res['transfer']= [
            'position'   => 41,
            'icon'       => 'fa fa-exchange',
            'url'        => route('vendor.transfer'),
            'title'      => __("Balance Transfer"),
            'children'   => [
                'transfer_list'=>[
                    'url'   => route('vendor.transfer_list'),
                    'title' => __("List Transfer"),
                ],
                'transfer'=>[
                    'url'        => route('vendor.transfer'),
                    'title'      => __("Balance Transfer"),
                ],
            ]
        ];

        if(setting_item('inbox_enable')) {
            $count = auth()->user()->unseen_message_count;
            $res['chat'] = [
                'position' => 20,
                'icon' => 'fa fa-comments',
                'url' => route('user.chat'),
                'title' => __("Messages :count",['count'=>$count ? sprintf('<span class="badge badge-danger">%d</span>',$count) : '']),
            ];
        }

        return $res;
    }
}
