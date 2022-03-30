@extends('admin.layouts.app')
@section('content')
<style>
    .btn-field-upload{
        display:none!important;
    }
    .form-control:disabled{
        background: #fff!important;
    }
    .upload-actions{
        display:none!important;
    }
    .attach-demo123{
        width: 100%;
    }
    .attach-demo123 img{
        width: 100%;
    }
</style>
    <form action="{{url('admin/module/user/store/'.($row->id ?? -1))}}" method="post" class="needs-validation" novalidate>
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? 'Edit: '.$row->getDisplayName() : 'Add new user'}}</h1>
                </div>
            </div>
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('User Info')}}</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Business name")}}</label>
                                        <input  disabled type="text" value="{{old('business_name',$row->business_name)}}" name="business_name" placeholder="{{__("Business name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('E-mail')}}</label>
                                        <input  disabled type="email" required value="{{old('email',$row->email)}}" placeholder="{{ __('Email')}}" name="email" class="form-control"  >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("User name")}}</label>
                                        <input  disabled type="text" name="user_name" required value="{{old('user_name',str_pad($row->id ?? $user_end , 5, "0", STR_PAD_LEFT))}}" placeholder="{{__("User name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label>{{__("Agent")}}</label>
                                    <input  disabled type="text" name="user_name" required value="{{old('user_name',$row->agent->name ?? "")}}" placeholder="{{__("User name")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("First name")}}</label>
                                        <input  disabled type="text" required value="{{old('first_name',$row->first_name)}}" name="first_name" placeholder="{{__("First name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Last name")}}</label>
                                        <input  disabled type="text" required value="{{old('last_name',$row->last_name)}}" name="last_name" placeholder="{{__("Last name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Phone Number')}}</label>
                                        <input  disabled type="text" value="{{old('phone',$row->phone)}}" placeholder="{{ __('Phone')}}" name="phone" class="form-control" required   >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Birthday')}}</label>
                                        <input  disabled type="text" value="{{ old('birthday',$row->birthday ? date("Y/m/d",strtotime($row->birthday)) :'') }}" placeholder="{{ __('Birthday')}}" name="birthday" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 1')}}</label>
                                        <div id="pac-container">
                                            <input  disabled type="text" value="{{old('address',$row->address)}}" placeholder="{{ __('Address')}}" name="address" class="form-control" id="pac-input" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 2')}}</label>
                                        <input  disabled type="text" value="{{old('address2',$row->address2)}}" placeholder="{{ __('Address 2')}}" name="address2" class="form-control" id="address2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("City")}}</label>
                                        <input  disabled type="text" value="{{old('city',$row->city)}}" name="city" placeholder="{{__("City")}}" class="form-control" id="city">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("State")}}</label>
                                        <input  disabled type="text" value="{{old('state',$row->state)}}" name="state" placeholder="{{__("State")}}" class="form-control" id="state">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="">{{__("Country")}}</label>
                                        @php
                                            $country = '';
                                            foreach(get_country_lists() as $id => $name){
                                                if($row->country==$id){
                                                    $country = $name;
                                                }
                                            }
                                        @endphp
                                        <input  disabled type="text" value="{{old('country' ,$country)}}" name="country" placeholder="{{__("Country")}}" class="form-control" id="country">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__("Zip Code")}}</label>
                                        <input  disabled type="text" value="{{old('zip_code',$row->zip_code)}}" name="zip_code" placeholder="{{__("Zip Code")}}" id="zip_code" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__("Customer")}}</label>
                                        <input  disabled type="text" value="{{old('customer', $row->customer ? __('Yes') : __('No'))}}" name="customer" class="form-control" id="customer">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="">{{__("Location")}}</label>
                                        <input  disabled type="text" value="{{old('location_id', $row->location ? $row->location->name : '')}}" name="location_id" class="form-control" id="location_id">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('Biographical')}}</label>
                                <div class="">
                                    <textarea name="bio" disabled class="d-none has-ckeditor disabled" cols="30" rows="10">{{old('bio',$row->bio)}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Publish')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{__('Status')}}</label>
                                @php
                                    $status = "";
                                    if($row->status =='publish'){
                                        $status = __('Active');
                                    }elseif($row->status =='blocked'){
                                        $status = __('Blocked');
                                    }
                                @endphp
                                <input  disabled type="text" value="{{old('status', $status)}}" name="status" class="form-control" id="status">
                            </div>
                            <div class="form-group">
                                <label>{{__('Role')}}</label>
                                @php
                                    $rol = '';
                                    foreach($roles as $role){
                                        if($row->hasRole($role)){
                                            $rol = ucfirst($role->name);
                                        }
                                    }
                                @endphp
                                <input  disabled type="text" value="{{old('role_id', $rol)}}" name="role_id" class="form-control" id="role_id">
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Vendor')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{__('Vendor Commission Type')}}</label>
                                @php
                                    $vendor_commission_type = __("Default");
                                    if($row->vendor_commission_type ?? '' == 'percent'){
                                        $vendor_commission_type = __('Percent');
                                    }else if($row->vendor_commission_type ?? '' == 'amount'){
                                        $vendor_commission_type = __('Amount');
                                    }else if($row->vendor_commission_type ?? '' == 'disable'){
                                        $vendor_commission_type = __('Disable Commission');
                                    }
                                @endphp
                                <input  disabled type="text" value="{{old('vendor_commission_type', $vendor_commission_type)}}" name="vendor_commission_type" class="form-control" id="vendor_commission_type">
                            </div>
                            <div class="form-group">
                                <label>{{__('Vendor commission value')}}</label>
                                <div class="form-controls">
                                    <input  disabled type="text" class="form-control" name="vendor_commission_amount" value="{{old("vendor_commission_amount",($row->vendor_commission_amount ?? '')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Avatar')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                {!! \Modules\Media\Helpers\FileHelper::fieldUpload('avatar_id',old('avatar_id',$row->avatar_id)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section ('script.body')
    <script>
        $('.attach-demo').addClass('attach-demo123');
        $('.attach-demo').removeClass('attach-demo');
    </script>
@endsection
