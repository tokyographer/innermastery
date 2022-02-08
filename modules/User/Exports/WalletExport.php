<?php
namespace Modules\User\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\User\Models\Wallet\DepositPayment;

class WalletExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        $query = DepositPayment::query();
        $query->where('object_model','wallet_deposit')->orderBy('id','desc');
        return $query->get();
    }

    public function map($user): array
    {
        return [
            $user->user->display_name,
            $user->user->agent ? $user->user->agent->display_name : __('without Asesor'),
            format_money_main($user->amount),
            $user->getMeta('credit'),
            $user->statusName,
            $user->gatewayObj ? $user->gatewayObj->getDisplayName() : '',
            display_datetime($user->updated_at)
        ];
    }

    public function headings(): array
    {
        return [
            __('Customer'),
            __('Asesor'),
            __('Amount'),
            __('Credit'),
            __('Status'),
            __('Payment Method'),
            __('Created At')
        ];
    }
}
