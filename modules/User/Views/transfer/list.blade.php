@extends('layouts.user')
@section('head')

@endsection
@section('content')
    <h2 class="title-bar no-border-bottom">
        {{ $page_title }}
    </h2>
    @include('admin.message')
    <div class="booking-history-manager">
        <div class="tabbable">
            @if(!empty($rows) and $rows->total() > 0)
                <div class="tab-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-booking-history">
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
                    <div class="bravo-pagination">
                        {{$rows->appends(request()->query())->links()}}
                    </div>
                </div>
            @else
                {{__("No data")}}
            @endif
        </div>
    </div>
@endsection
@section('footer')

@endsection
