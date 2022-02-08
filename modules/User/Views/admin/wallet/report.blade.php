@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__('Credit Purchase Report')}}</h1>
            <div class="title-actions">
                <a class="btn btn-warning btn-icon" href="{{ route("user.admin.wallet.export") }}" target="_blank" title="{{ __("Export to excel") }}">
                    <i class="icon ion-md-cloud-download"></i> {{ __("Export to excel") }}
                </a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{route('user.admin.wallet.reportBulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        {{csrf_field()}}
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                            <option value="completed">{{__("Mark as completed")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-left">
                <form method="get" action="" class="filter-form filter-form-right d-flex justify-content-end">
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 400px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                        <input type="hidden" name="from">
                        <input type="hidden" name="to">
                    </div>

                    <?php
                        $user = !empty(Request()->agent_id) ? App\User::find(Request()->agent_id) : false;
                        \App\Helpers\AdminForm::select2('agent_id', [
                            'configs' => [
                                'ajax'        => [
                                    'url'      => url('/admin/module/user/getForAgent'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('Agent')
                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->name_or_email . ' (#' . $user->id . ')'
                        ] : false)
                    ?>
                    <select name="status" class="form-control">
                        <option value="">{{__("-- Status --")}}</option>
                        <option @if(request()->query('status') == 'fail') selected @endif value="fail">{{__("Failed")}}</option>
                        <option @if(request()->query('status') == 'processing') selected @endif value="processing">{{__("Processing")}}</option>
                        <option @if(request()->query('status') == 'completed') selected @endif value="completed">{{__("Completed")}}</option>
                        <option @if(request()->query('status') == 'draft') selected @endif value="draft">{{__("Draft")}}</option>
                    </select>
                    @csrf
                        <?php
                        $user = !empty(Request()->user_id) ? App\User::find(Request()->user_id) : false;
                        \App\Helpers\AdminForm::select2('user_id', [
                            'configs' => [
                                'ajax'        => [
                                    'url'      => url('/admin/module/user/getForSelect2'),
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
            <div class="panel-title">{{__('Purchase logs')}}</div>
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <table class="table table-hover bravo-list-item">
                        <thead>
                        <tr>
                            <th width="80px"><input type="checkbox" class="check-all"></th>
                            <th>{{__('Customer')}}</th>
                            <th>{{__('Asesor')}}</th>
                            <th width="80px">{{__('Amount')}}</th>
                            <th width="80px">{{__('Credit')}}</th>
                            <th width="80px">{{__('Status')}}</th>
                            <th width="150px">{{__('Payment Method')}}</th>
                            <th width="120px">{{__('Created At')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td><input type="checkbox" class="check-item" name="ids[]" value="{{$row->id}}">
                                    #{{$row->id}}</td>
                                <td>
                                    @if($row->user)
                                        <a href="javascript:void(0)">{{$row->user->display_name}}</a>
                                    @endif
                                </td>
                                <td>
                                    @if($row->user->agent)
                                        <a href="javascript:void(0)">{{$row->user->agent->display_name}}</a>
                                    @else
                                        {{ __('without Asesor') }}
                                    @endif
                                </td>
                                <td>{{format_money_main($row->amount)}}</td>
                                <td>{{$row->getMeta('credit')}}</td>
                                <td>
                                    <span class="label label-{{$row->status}}">{{$row->statusName}}</span>
                                </td>
                                <td>
                                    {{$row->gatewayObj ? $row->gatewayObj->getDisplayName() : ''}}
                                </td>
                                <td>{{display_datetime($row->updated_at)}}</td>
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
