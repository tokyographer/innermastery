<?php
namespace Modules\User\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\User;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return User::with("agent")->select([
            'business_name',
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'address2',
            'city',
            'state',
            'country',
            'zip_code',
            'status',
            'agent_id'
        ])->get();
    }

    public function map($user): array
    {
        $agent = "";
        if(isset($user->agent->first_name)){
            $agent = ltrim($user->agent->first_name . ' ' . $user->agent->last_name, "=-");
        }
        return [
            ltrim($user->business_name,"=-"),
            ltrim(str_pad($user->id, 5, "0", STR_PAD_LEFT),"=-"),
            ltrim($user->first_name,"=-"),
            ltrim($user->last_name,"=-"),
            ltrim($user->email,"=-"),
            ltrim($user->phone,"=-"),
            ltrim($user->address,"=-"),
            ltrim($user->address2,"=-"),
            ltrim($user->city,"=-"),
            ltrim($user->state,"=-"),
            ltrim($user->country,"=-"),
            ltrim($user->zip_code,"=-"),
            ltrim($user->status,"=-"),
            $agent
        ];
    }

    public function headings(): array
    {
        return [
            __('Business Name'),
            '#',
            __('First name'),
            __('Last name'),
            __('Email'),
            __('Phone'),
            __('Address'),
            __('Address 2'),
            __('City'),
            __('State'),
            __('Country'),
            __('Zip Code'),
            __('Status'),
            __('Agent'),
        ];
    }
}
