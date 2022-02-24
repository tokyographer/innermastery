@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__('Customer')}}</h1>
            {{-- <div class="title-actions">
                <a class="btn btn-warning btn-icon" href="{{ route("user.admin.wallet.export") }}" target="_blank" title="{{ __("Export to excel") }}">
                    <i class="icon ion-md-cloud-download"></i> {{ __("Export to excel") }}
                </a>
            </div> --}}
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between">
            <div class="col-left"></div>
            <div class="col-left">
                <form method="get" action="" class="filter-form filter-form-right d-flex justify-content-end">
                    @csrf
                        <?php
                        $user = !empty(Request()->user_id) ? App\User::find(Request()->user_id) : false;
                        \App\Helpers\AdminForm::select2('user_id', [
                            'configs' => [
                                'ajax'        => [
                                    'url'      => url('/admin/module/user/getForCustomer'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('-- User --')
                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->name_or_email . ' (#' . $user->id . ')'
                        ] : false)
                        ?>
                    <button class="btn-info btn btn-icon" type="submit">{{__('Filter')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel booking-history-manager">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="60px"><input type="checkbox" class="check-all"></th>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Email')}}</th>
                            <th>{{__('Credit')}}</th>
                            <th>{{__('Phone')}}</th>
                            <th>{{__('Role')}}</th>
                            <th>{{__('Asesor')}}</th>
                            <th class="date">{{ __('Date')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item"></td>
                                <td class="title">
                                    <a href="{{url('admin/module/user/edit/'.$row->id)}}">{{$row->getDisplayName()}}</a>
                                </td>
                                <td>{{$row->email}}</td>
                                <td>{{$row->balance}}</td>
                                <td>{{$row->phone}}</td>
                                <td>
                                    @php $roles = $row->getRoleNames();
                                    if(!empty($roles[0])){
                                        echo e(ucfirst(__($roles[0])));
                                    }
                                    @endphp
                                </td>
                                <td>Admin(Pendiente)</td>
                                <td>{{ display_date($row->created_at)}}</td>
                                {{--<td class="status">{{$row->status}}</td>--}}
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-th"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item"  href="{{url('admin/module/user/edit/'.$row->id)}}"><i class="fa fa-edit"></i> {{__('Edit')}}</a>
                                            @if(!$row->hasVerifiedEmail())
                                                <a class="dropdown-item"  href="{{route('user.admin.verifyEmail',$row)}}"><i class="fa fa-edit"></i> {{__('Verify email')}}</a>
                                                @else
                                                <a class="dropdown-item"  href="#" ><i class="fa fa-check"></i> {{__('Email verified')}}</a>
                                            @endif
                                            <a class="dropdown-item" href="{{url('admin/module/user/password/'.$row->id)}}"><i class="fa fa-lock"></i> {{__('Change Password')}}</a>
                                            <a class="dropdown-item" href="https://beyondinner.xweb.live/admin/module/user/wallet/report?status=completed&_token=KfY7vwoAg5HirDxi0iNIknMl65dKLRH4WXruZGGI&user_id={{$row->id}}"><i class="ion-md-bookmarks"></i> {{__('Sales History')}}</a>
											<a href="{{route('user.admin.wallet.addCredit',['id'=>$row->id])}}" class="dropdown-item"><i class="fa fa-plus"></i> {{__("Add Credit")}}</a>
											<a href="{{route('user.admin.wallet.removeCredit',['id'=>$row->id])}}" class="dropdown-item"><i class="fa fa-minus"></i> {{__("Remove Credit")}}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{$rows->links()}}
        </div>
    </div>
@endsection


@section('script.body')
    <script src="{{url('libs/chart_js/Chart.min.js')}}"></script>
    <script src="{{url('libs/daterange/moment.min.js')}}"></script>
    <script src="{{url('libs/daterange/daterangepicker.min.js?_ver='.config('app.version'))}}"></script>
    <link rel="stylesheet" href="{{url('libs/daterange/daterangepicker.css')}}"/>

    <script type="text/javascript">
        $(function() {
            var start_re = "{{ request()->from }}";
            var end_re = "{{ request()->to }}";

            var start = moment().subtract(29, 'days');
            var end = moment();

            if(start_re && end_re){
                start = moment(start_re);
                end = moment(end_re);
            }

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#reportrange input[name=from]').val(start.format('YYYY-MM-DD'));
                $('#reportrange input[name=to]').val(end.format('YYYY-MM-DD'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    '{{__("Today")}}': [moment(), moment()],
                    '{{__("Yesterday")}}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '{{__("Last 7 Days")}}': [moment().subtract(6, 'days'), moment()],
                    '{{__("Last 30 Days")}}': [moment().subtract(29, 'days'), moment()],
                    '{{__("This Month")}}': [moment().startOf('month'), moment().endOf('month')],
                    '{{__("Last Month")}}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    '{{__("This Year")}}': [moment().startOf('year'), moment().endOf('year')],
                    '{{__('This Week')}}': [moment().startOf('week'), end]
                }
            }, cb).on('apply.daterangepicker', function (ev, picker) {
                $('#reportrange input[name=from]').val(picker.startDate.format('YYYY-MM-DD'));
                $('#reportrange input[name=to]').val(picker.endDate.format('YYYY-MM-DD'));
            });

            cb(start, end);

        });
    </script>
@endsection
