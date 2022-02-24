@extends('admin.layouts.app')
@section('content')
    <h2 class="title-bar">
        {{__("Balance Transfer")}}
    </h2>
    @include('admin.message')
    <form action="{{route('user.admin.transfer_store')}}" method="post" class="input-has-icon bravo-user-dashboard form-transfer-coins">
        @csrf
        <div class="row dashboard-price-info row-eq-height mb-5">
            <div class="col-lg-3 col-md-3">
                <div class="dashboard-item">
                    <div class="wrap-box">
                        <div class="title">
                            {{__("Credit balance")}}
                        </div>
                        <div class="details">
                            <div class="number">{{__(':amount',['amount'=>$row->balance])}}</div>
                        </div>
                        @if($row->balance)
                        <div class="desc">~ {{format_money(credit_to_money($row->balance))}} </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="form-group style-height">
                    <label>{{__("Select User")}}</label>
                    <?php
                        $user = !empty($row->create_user) ? App\User::find($row->create_user) : false;
                        \App\Helpers\AdminForm::select2('email', [
                            'configs' => [
                                'ajax'        => [
                                    'url' => url('/admin/module/user/getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('-- Select User --'),

                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->getDisplayName() . ' (#' . $user->id . ')'
                        ] : false)
                    ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__("Write Price")}}</label>
                    <input type="number" name="price" placeholder="{{__("Write Price")}}" class="form-control coins_price_quantity">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{__("Payment Concept")}}</label>
                    <textarea type="number" name="payment_concept" placeholder="{{__("Payment Concept")}}" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <hr>
                <button class="btn btn-primary btn-transfer-coins" type="submit"><i class="fa fa-save"></i> {{__('Transfer')}}</button>
            </div>
        </div>
    </form>
@endsection
@section('script.head')
    <style>
        .dashboard-item {
            padding: 21px 32px;
            background-color: #fff;
            min-height: 154px;
            position: relative;
            height: 100%;
        }
        .wrap-box {
            text-align: center;
        }
        .wrap-box .title {
            color: #1a2b48;
            letter-spacing: 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
            text-align: center;
        }
        .wrap-box .number {
            color: #1a2b48;
            letter-spacing: 0;
            font-size: 36px;
            text-align: center;
        }
        .title-bar {
            display: block;
            padding: 20px 0;
            margin: 0 0 15px;
            border-bottom: 1px solid #ccc;
            position: relative;
        }
    </style>
@endsection
@section('script.body')
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
    <script>
        $(document).on("keyup", ".coins_price_quantity", function(){
            const coins = "{{ $row->balance }}";
            let quantity = $(this).val();
            if(parseInt(coins) < parseInt(quantity)){
                alertify.error('{{__("You don not have enough :amount for payout", ["amount"=>format_money($row->balance)])}}');
                $(this).val(coins);
            }
        });
        $(document).on("click", ".btn-transfer-coins", function(e){
            e.preventDefault();
            let quantity = $(".coins_price_quantity").val();
            let email = $(".coins_user").val();
            if(parseInt(quantity) <= 0 || quantity.length == 0){
                alertify.error('{{__("The amount to transfer must be greater than 0")}}');
                return;
            }
            if(email == "{{ $row->email }}"){
                alertify.error('{{__("Error Email")}}');
                return;
            }
            $('.form-transfer-coins').submit();
        });
    </script>
@endsection
