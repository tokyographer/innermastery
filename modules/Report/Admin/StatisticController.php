<?php
namespace Modules\Report\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Booking\Emails\NewBookingEmail;
use Modules\Booking\Models\Booking;
use App\User;

class StatisticController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $f = strtotime('monday this week');
        $status = config('booking.statuses');
        $data = [
            'earning_chart_data'  => Booking::getStatisticChartData($f, time(), $status)['chart'],
            'earning_detail_data' => Booking::getStatisticChartData($f, time(), $status)['detail']
        ];
        return view('Report::admin.statistic.index', $data);
    }

    public function customer()
    {
        $query = User::where('customer', 1);
        $data = [
            'rows'=> $query->paginate(20),
            'page_title'=>__("Customer"),
            'breadcrumbs'=>[
                [
                    'url'=>'#',
                    'name'=>__('Customer'),
                ],
            ]
        ];
        return view('Report::admin.customer.index', $data);
    }

    public function reloadChart(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $status = config('booking.statuses');
        $customer_id = false;
        $vendor_id = false;
        $user_type = $request->input('user_type');
        if ($user_type == 'customer') {
            $customer_id = $request->input('user_id');
        }
        if ($user_type == 'vendor') {
            $vendor_id = $request->input('user_id');
        }
        return $this->sendSuccess([
            'chart_data'  => Booking::getStatisticChartData(strtotime($from), strtotime($to), $status, $customer_id, $vendor_id)['chart'],
            'detail_data' => Booking::getStatisticChartData(strtotime($from), strtotime($to), $status, $customer_id, $vendor_id)['detail']
        ]);
    }
}
