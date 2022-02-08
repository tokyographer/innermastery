@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('List Transfer')}}</h1>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
            </div>
            <div class="col-left">
                <form method="get" class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    {{-- <?php
                        $user = !empty(request()->from) ? App\User::find(request()->from) : false;
                        \App\Helpers\AdminForm::select2('from', [
                            'configs' => [
                                'ajax'        => [
                                    'url' => url('/admin/module/user/getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('From'),

                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->getDisplayName() . ' (#' . $user->id . ')'
                        ] : false)
                    ?> --}}
                    <?php
                        $user = !empty(request()->to) ? App\User::find(request()->to) : false;
                        \App\Helpers\AdminForm::select2('to', [
                            'configs' => [
                                'ajax'        => [
                                    'url' => url('/admin/module/user/getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('To'),

                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->getDisplayName() . ' (#' . $user->id . ')'
                        ] : false)
                    ?>
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search User')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{__("From")}}</th>
                                    <th>{{__("To")}}</th>
                                    <th>{{__("Status")}}</th>
                                    <th>{{__("Uuid")}}</th>
                                    <th>{{__("Coins")}}</th>
                                    <th>{{__("Date Create")}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($rows->total() > 0)
                                    @foreach($rows as $row)
                                        <tr>
                                            <td>
                                                <small>{{$row->from->name}} (#{{$row->from->id}})</small>
                                            </td>
                                            <td>
                                                <small>{{$row->to->name}} (#{{$row->to->id}})</small>
                                            </td>
                                            <td>
                                                <small>{{$row->status}}</small>
                                            </td>
                                            <td>
                                                <small>{{$row->uuid}}</small>
                                            </td>
                                            <td>
                                                <small>{{$row->coins}}</small>
                                            </td>
                                            <td>
                                                <small>{{$row->created_at->diffForHumans()}}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6">{{__("No data")}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </form>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
@endsection
