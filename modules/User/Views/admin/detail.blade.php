@extends('admin.layouts.app')

@section('content')
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
                                        <input type="text" value="{{old('business_name',$row->business_name)}}" name="business_name" placeholder="{{__("Business name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('E-mail')}}</label>
                                        <input type="email" required value="{{old('email',$row->email)}}" placeholder="{{ __('Email')}}" name="email" class="form-control"  >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("User name")}}</label>
                                        <input type="text" name="user_name" required value="{{old('user_name',str_pad($row->id ?? $user_end , 5, "0", STR_PAD_LEFT))}}" placeholder="{{__("User name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label>{{__("Agent")}}</label>
                                        <?php
                                            $user = !empty($row->agent_id) ? App\User::find($row->agent_id) : false;
                                            \App\Helpers\AdminForm::select2('agent_id', [
                                                'configs' => [
                                                    'ajax'        => [
                                                        'url'      => url('/admin/module/user/getForAsesor'),
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
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("First name")}}</label>
                                        <input type="text" required value="{{old('first_name',$row->first_name)}}" name="first_name" placeholder="{{__("First name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Last name")}}</label>
                                        <input type="text" required value="{{old('last_name',$row->last_name)}}" name="last_name" placeholder="{{__("Last name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Phone Number')}}</label>
                                        <input type="text" value="{{old('phone',$row->phone)}}" placeholder="{{ __('Phone')}}" name="phone" class="form-control" required   >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Birthday')}}</label>
                                        <input type="text" value="{{ old('birthday',$row->birthday ? date("Y/m/d",strtotime($row->birthday)) :'') }}" placeholder="{{ __('Birthday')}}" name="birthday" class="form-control has-datepicker input-group date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 1')}}</label>
                                        <div id="pac-container">
                                            <input type="text" value="{{old('address',$row->address)}}" placeholder="{{ __('Address')}}" name="address" class="form-control" id="pac-input" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 2')}}</label>
                                        <input type="text" value="{{old('address2',$row->address2)}}" placeholder="{{ __('Address 2')}}" name="address2" class="form-control" id="address2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("City")}}</label>
                                        <input type="text" value="{{old('city',$row->city)}}" name="city" placeholder="{{__("City")}}" class="form-control" id="city">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("State")}}</label>
                                        <input type="text" value="{{old('state',$row->state)}}" name="state" placeholder="{{__("State")}}" class="form-control" id="state">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="">{{__("Country")}}</label>
                                        <select name="country" class="form-control" id="country-sms-testing" required>
                                            <option value="">{{__('-- Select --')}}</option>
                                            @foreach(get_country_lists() as $id=>$name)
                                                <option @if($row->country==$id) selected @endif value="{{$id}}">{{$name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__("Zip Code")}}</label>
                                        <input type="text" value="{{old('zip_code',$row->zip_code)}}" name="zip_code" placeholder="{{__("Zip Code")}}" id="zip_code" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__("Customer")}}</label>
                                        <select name="customer" class="form-control" required>
                                            <option value="1">{{__('Yes')}}</option>
                                            <option value="0" selected>{{__('No')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="">{{__("Location")}}</label>

                                        <?php
                                            $location = !empty($row->location_id) ? Modules\Location\Models\Location::find($row->location_id) : false;
                                            \App\Helpers\AdminForm::select2('location_id', [
                                                'configs' => [
                                                    'ajax'        => [
                                                        'url'      => url('admin/module/location/getForLocation'),
                                                        'dataType' => 'json'
                                                    ],
                                                    'allowClear'  => true,
                                                    'placeholder' => __('Location')
                                                ]
                                            ], !empty($location->id) ? [
                                                $location->id,
                                                $location->name . ' (#' . $location->id . ')'
                                            ] : false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('Biographical')}}</label>
                                <div class="">
                                    <textarea name="bio" class="d-none has-ckeditor" cols="30" rows="10">{{old('bio',$row->bio)}}</textarea>
                                </div>
                            </div>
                            @if(isset($row->id))
                                <div class="form-group">
                                    <a href="{{ route('user.admin.wallet.report') . '?user_id=' . $row->id }}" target="_blank" class="btn btn-success" style="width:100%;height:60px;line-height:3">Ver historial de usuario</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Publish')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{__('Status')}}</label>
                                <select required class="custom-select" name="status">
                                    <option value="">{{ __('-- Select --')}}</option>
                                    <option @if(old('status',$row->status) =='publish') selected @endif value="publish">{{ __('Active')}}</option>
                                    <option @if(old('status',$row->status) =='blocked') selected @endif value="blocked">{{ __('Blocked')}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{__('Role')}}</label>
                                <select required class="custom-select" name="role_id">
                                    <option value="">{{ __('-- Select --')}}</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}" @if(!old('role_id') && $row->hasRole($role)) selected @elseif(old('role_id')  == $role->id ) selected @endif >{{ucfirst($role->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Vendor')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{__('Vendor Commission Type')}}</label>
                                <div class="form-controls">
                                    <select name="vendor_commission_type" class="form-control">
                                        <option value="">{{__("Default")}}</option>
                                        <option value="percent" {{old("vendor_commission_type",($row->vendor_commission_type ?? '')) == 'percent' ? 'selected' : ''  }}>{{__('Percent')}}</option>
                                        <option value="amount" {{old("vendor_commission_type",($row->vendor_commission_type ?? '')) == 'amount' ? 'selected' : ''  }}>{{__('Amount')}}</option>
                                        <option value="disable" {{old("vendor_commission_type",($row->vendor_commission_type ?? '')) == 'disable' ? 'selected' : ''  }}>{{__('Disable Commission')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{__('Vendor commission value')}}</label>
                                <div class="form-controls">
                                    <input type="text" class="form-control" name="vendor_commission_amount" value="{{old("vendor_commission_amount",($row->vendor_commission_amount ?? '')) }}">
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
                    <div class="panel text-center">
                        <div class="panel-title"><strong>{{ __('Link')}}</strong></div>
                        <button type="button" class="btn btn-success m-3 btn_link">{{ __('Copy link') }}</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span></span>
                <button class="btn btn-primary" type="submit">{{ __('Save Change')}}</button>
            </div>
        </div>
    </form>

@endsection
@section ('script.body')
    <script async src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API') }}&libraries=places&callback=initMap"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <script>
        function initAutocomplete() {
            const input = document.getElementById("pac-input");
            const options = {
                fields: ["address_components", "geometry", "icon", "name"],
                strictBounds: false,
                types: ["establishment"],
            };
            const autocomplete = new google.maps.places.Autocomplete(input, options);

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                const place = autocomplete.getPlace();
                $('#city').val("");
                $('#state').val("");
                $('#country-sms-testing').val("");
                $('#address2').val("");
                $('#zip_code').val("");

                for (const component of place.address_components) {
                    const componentType = component.types[0];

                    switch (componentType) {
                        case "locality":
                            $('#city').val(component.long_name);
                            break;
                        case "administrative_area_level_1":
                            $('#state').val(component.short_name);
                            break;
                        case "country":
                            $('#country-sms-testing').val(component.short_name);
                            break;
                        case "sublocality_level_1":
                            $('#address2').val(component.long_name);
                            break;
                        case "route":
                            $('#address2').val(component.long_name);
                            break;
                        case "postal_code":
                            $('#zip_code').val(component.long_name);
                            break;
                    }
                }
            });
        }
        $(document).on('keyup', '#pac-input', function(){
            initAutocomplete();
        });
        $(document).on('click', '.btn_link', function(element) {
            var $temp = $("<textarea>");
            $("body").append($temp);
            $temp.val('{{ route('user.admin.user_view_only', md5($row->id)) }}').select();
            document.execCommand("copy");
            $temp.remove();

            alertify.success("Link copiado");
        });
    </script>
@endsection
