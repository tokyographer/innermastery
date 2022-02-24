@extends('layouts.user')
@section('head')
    <style>
        .style-height .select2-selection{
            height: 35px;
        }
        .style-height #select2-select_create_user-container{
            line-height: 30px;
        }
    </style>
@endsection
@section('content')
    <h2 class="title-bar">
        {{__("Balance Transfer")}}
    </h2>
    @include('admin.message')
    <form action="{{route('vendor.transfer_store')}}" method="post" class="input-has-icon bravo-user-dashboard form-transfer-coins">
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
                    <input type="text" name="email" placeholder="{{__("Search by email")}}" class="form-control coins_user">
                    <i class="fa fa-user input-icon" style="bottom: 0px"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__("Write Price")}}</label>
                    <input type="number" name="price" placeholder="{{__("Write Price")}}" class="form-control coins_price_quantity">
                    <i class="fa fa-money input-icon" style="bottom: -1px"></i>
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
@section('footer')
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
            $.ajax({
                    url: "{{route('vendor.transfer_check_email')}}",
                    data: $('.form-transfer-coins').serialize(),
                    method:"post",
                    success: function (res) {
                        if(!res.status) {
                            alertify.error(res.msg)
                            return
                        }
                        $('.form-transfer-coins').submit();
                    }
                });
        });
    </script>
@endsection
