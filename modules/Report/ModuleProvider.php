<?php


namespace Modules\Report;

use Modules\User\Models\Wallet\DepositPayment;

class ModuleProvider extends \Modules\ModuleServiceProvider
{
    public function register()
    {

        $this->app->register(RouteServiceProvider::class);
    }
    public static function getAdminMenu()
    {
        $count = 0;
        $pending_purchase = DepositPayment::countPending();
        $count += $pending_purchase;
        return [
            'transfer'=>[
                "position"=>59,
                'url'        => route('user.admin.transfer'),
                'title'      =>  __("Balance Transfer"),
                'icon'       => 'fa fa-exchange',
                'children'   => [
                    'transfer_list'=>[
                        'url'   => route('user.admin.transfer_list'),
                        'title' => __("List Transfer"),
                    ],
                    'transfer'=>[
                        'url'        => route('user.admin.transfer'),
                        'title'      => __("Balance Transfer"),
                    ],
                ]
            ],
            'sale'=>[
                "position"=>60,
                'url'        => 'admin/module/report/booking',
                'title'      =>  __('Sales :count',['count'=>$count ? sprintf('<span class="badge badge-warning">%d</span>',$count) : '']),
                'icon'       => 'icon ion-ios-pie',
                'permission' => 'report_view',
                'children'   => [
                    'buy_credit_report'=>[
                        'parent'=>'report',
                        'url'=>route('user.admin.wallet.report'),
                        'title'=>__("Credit Purchase Report :count",['count'=>$pending_purchase ? sprintf('<span class="badge badge-warning">%d</span>',$pending_purchase) : '']),
                    ],
                    'booking'=>[
                        'url'        => 'admin/module/report/booking',
                        'title'      => __('Booking Reports'),
                        'permission' => 'report_view',
                    ],
					'coupon'=>[
						"position"=>51,
						'url'        => route('coupon.admin.index'),
						'title'      => __('Coupon'),
						'permission' => 'coupon_view',
					],
                    'enquiry'=>[
                        'url'        => 'admin/module/report/enquiry',
                        'title'      => __('Enquiry Reports'),
                        'permission' => 'report_view',
                    ],
                    'contact'=>[
                        'url'        => 'admin/module/contact',
                        'title'      => __('Contact Submissions'),
                        'permission' => 'contact_manage',
                    ],
					'payout'=>[
						'url'        => 'admin/module/vendor/payout',
						'title'      => __("Payouts :count",['count'=>$count ? sprintf('<span class="badge badge-warning">%d</span>',$count) : '']),
						'permission' => 'payouts_manage',
					],
					'payout'=>[
						'url'        => 'admin/module/core/settings/index/payment',
						'title'      => __("Payment Settings"),
						'permission' => 'payouts_manage',
					],
                ]
            ],
			'report'=>[
                "position"=>69,
                'url'        => 'admin/module/report/booking',
                'title'      =>  __('Reports :count',['count'=>$count ? sprintf('<span class="badge badge-warning">%d</span>',$count) : '']),
                'icon'       => 'icon ion-ios-pie',
                'permission' => 'report_view',
                'children'   => [
                    'statistic'=>[
                        'url'        => 'admin/module/report/statistic',
                        'title'      => __('Booking Statistic'),
                        'icon'       => 'icon ion ion-md-podium',
                        'permission' => 'report_view',
                    ]
                ]
            ],
        ];
    }
}
