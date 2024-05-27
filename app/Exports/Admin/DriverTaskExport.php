<?php

namespace App\Exports\Admin;

use App\Models\DriverTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class DriverTaskExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{

    private $from_task_date;
    private $to_task_date;
    private $task_status_id_list;
    private $delivery_office_id_list;
    private $orderby;
    /**
     * Undocumented function
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->from_task_date = $data['from_task_date'];
        $this->to_task_date = $data['to_task_date'];
        $this->task_status_id_list = $data['task_status_id_list'];
        $this->delivery_office_id_list = $data['delivery_office_id'];
        $this->orderby = $data['orderby'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $task_list_object = DriverTask::select()
            ->with(['joinOffice', 'joinDriver', 'joinTaskStatus']);

        // 稼働ステータス絞り込み
        if ($this->task_status_id_list) {
            $task_list_object->where(function($query) {
                foreach ($this->task_status_id_list as $task_status_id) {
                    $query->orWhere('driver_task_status_id', '=', $task_status_id);
                }
            });
        }

        // 稼働日 範囲 絞り込み
        if ($this->from_task_date) {
            $task_list_object->where('task_date', '>=', $this->from_task_date);
        }
        if ($this->to_task_date) {
            $task_list_object->where('task_date', '<=', $this->to_task_date);
        }

       if ($this->delivery_office_id_list) {
            $task_list_object->where(function($query) {
                foreach($this->delivery_office_id_list as $delivery_office_id){
                    $query->orWhere('delivery_office_id', '=', $delivery_office_id);
                }
            });
        }


        /* 並び替え */
        if ($this->orderby === 'id_desc') {
            $task_list_object->orderBy('id', 'desc');
        } elseif ($this->orderby === 'id_asc') {
            $task_list_object->orderBy('id', 'asc');
        } elseif ($this->orderby === 'task_date_desc') {
            $task_list_object->orderBy('task_date', 'asc');
        } elseif ($this->orderby === 'task_date_asc') {
            $task_list_object->orderBy('task_date', 'desc');
        } elseif ($this->orderby === 'created_at_desc') {
            $task_list_object->orderBy('created_at', 'desc');
        } elseif ($this->orderby === 'updated_at_asc') {
            $task_list_object->orderBy('updated_at', 'asc');
        } else {
            $task_list_object->orderBy('id', 'desc');
        }

        $task_list = $task_list_object->get();
        return $task_list;
    }

    public function headings(): array
    {
        return [
            'id',
            '稼働日',
            '申し込み日',
            'ステータス',
            '稼働依頼プラン',
            'ドライバーID',
            'ドライバー名',
            '営業所ID',
            '配送会社名',
            '営業所名',
            '稼働メモ',
            'システム利用料金',
            'システム利用料金(繁忙期)',
            '運賃',
            '緊急依頼料金',
            '値引き額',
            '税金',
            '決済手数料率',
            '返金した金額',
            '支払いステータス',
            '返金ステータス',
            '作成日',
            '更新日',
        ];
    }

    public function title(): string
    {
        return 'driver_task';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->taskDateYmd,
            $row->request_date,
            $row->joinTaskStatus->name ?? '',
            $row->joinDriverTaskPlan->name ?? '',
            $row->driver_id ?? '',
            ($row->joinDriver->name_sei ?? '') . ' ' . ($row->joinDriver->name_mei ?? ''),
            $row->delivery_office_id ?? '',
            $row->joinOffice->joinCompany->name ?? $row->joinOffice->delivery_company_name ?? '',
            $row->joinOffice->name ?? '',
            $row->task_memo ?? '',
            $row->system_price ?? '',
            $row->busy_system_price ?? '',
            $row->freight_cost ?? '',
            $row->emergency_price ?? '',
            $row->discount ?? '',
            $row->tax ?? '',
            $row->payment_fee_rate ?? '',
            $row->refund_amount ?? '',
            $row->joinTaskPaymentStatus->name ?? '',
            $row->joinTaskRefundStatus->name ?? '',
            $row->created_at,
            $row->updated_at,
        ];
    }
}
