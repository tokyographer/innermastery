@extends('admin.layouts.app')
@section('content')
    <form action="{{route('user.admin.role.store', ['id' => ($row->id) ? $row->id : '-1'])}}" method="post">
        @csrf
        @include('admin.message')
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? 'Edit: '.$row->name : 'Add new role'}}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-body">
                            <h3 class="panel-body-title">{{ __('Role Content')}} </h3>
                            <div class="form-group">
                                <label>{{ __('Name')}}</label>
                                <input type="text" value="{{$row->name}}" placeholder="{{ __('Role Name')}}" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Only Client')}}</label>
                                <select name="only_client" id="only_client" class="form-control">
                                    <option value="0" @if(!$row->only_client) selected @endif>{{ __('No') }}</option>
                                    <option value="1" @if($row->only_client) selected @endif>{{ __('Yes') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('User Client')}}</label>
                                <select name="client" id="client" class="form-control">
                                    <option value="0" @if(!$row->client) selected @endif>{{ __('No') }}</option>
                                    <option value="1" @if($row->client) selected @endif>{{ __('Yes') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Ordering')}}</label>
                                <select name="orderby" id="orderby" class="form-control">
                                    @foreach ($roles as $index => $role)
                                        <option value="{{ $index + 1 }}" @if(($index + 1) == $row->orderby) selected @endif>{{ $index + 1 }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>&nbsp;</span>
                        <button class="btn btn-primary" type="submit">{{ __('Save Change')}}</button>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection
@section ('script.body')
@endsection
