<?php

namespace App\Exports\Admin;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class DriverExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @todo 引数で 日付などを受け取って、whereで条件取得
     */
    public function __construct($data)
    {
        $this->orderby = $data['orderby'];
        $this->addr1_id = $data['addr1_id'];
        $this->gender_id = $data['gender_id'];
        $this->from_age = $data['from_age'];
        $this->to_age = $data['to_age'];
        $this->from_review_avg_score = $data['from_review_avg_score'];
        $this->to_review_avg_score = $data['to_review_avg_score'];
        $this->from_task_count = $data['from_task_count'];
        $this->to_task_count = $data['to_task_count'];
        $this->is_soft_delete = $data['is_soft_delete'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $driver_list_object = Driver::select(
            'id',
            'name_sei',
            'name_mei',
            'name_sei_kana',
            'name_mei_kana',
            'email',
            'birthday',
            'gender_id',
            'addr1_id',
            'addr2',
            'addr3',
            'addr4',
            'tel',
            'icon_img',
            'career',
            'introduction',
            'created_at',
            'updated_at',
            'deleted_at',
        )
            ->with(['joinAddr1', 'joinRegisterOffice']) // 結合
            ->withAvg('joinDriverReview', 'score') // 平均評価点
            ->withCount(['joinTask']); // 稼働数

        /* ソフトディレートを含める */
        if ($this->is_soft_delete) {
            $driver_list_object->withTrashed();
        }

        /* 絞り込み検索 */
        // 性別
        if ($this->gender_id) {
            $driver_list_object->where([['gender_id', $this->gender_id]]);
        }

        // 都道府県
        if ($this->addr1_id) {
            $driver_list_object->where([['addr1_id', $this->addr1_id]]);
        }

        // 平均評価点 絞り込み
        if ($this->from_review_avg_score || $this->to_review_avg_score) {
            // 平均評価以上 ~ 以下 で絞り込み処理 
            $driver_list_object->havingBetween('join_driver_review_avg_score', [$this->from_review_avg_score, $this->to_review_avg_score]);
        }

        //  稼働数 絞り込み
        if ($this->from_task_count || $this->to_task_count) {

            // 稼働数 以上 ~ 以下 で絞り込み処理 
            $driver_list_object->havingBetween('join_task_count', [$this->from_task_countt, $this->to_task_count]);
        }

        // 年齢 範囲 絞り込み
        // 20歳上というのは 現在 - 20の日付 以下という意味
        if ($this->from_age) {
            $date = new \DateTime();
            $search_from_birthday = $date->modify("-{$this->from_age} year")->format('Y-m-d');

            $driver_list_object->whereDate('birthday', '<=', $search_from_birthday);
        }
        if ($this->to_age) {
            $date = new \DateTime();
            $search_to_birthday = $date->modify("-{$this->to_age} year")->format('Y-m-d');

            $driver_list_object->whereDate('birthday', '<=', $search_to_birthday);
        }


        /* 並び替え */
        if ($this->orderby === 'id_desc') {
            $driver_list_object->orderBy('id', 'desc');
        } elseif ($this->orderby === 'id_asc') {
            $driver_list_object->orderBy('id', 'asc');
        } elseif ($this->orderby === 'join_driver_review_avg_score_desc') {
            $driver_list_object->orderBy('join_driver_review_avg_score', 'desc');
        } elseif ($this->orderby === 'join_driver_review_avg_score_asc') {
            $driver_list_object->orderBy('join_driver_review_avg_score', 'asc');
        } elseif ($this->orderby === 'join_task_count_desc') {
            $driver_list_object->orderBy('join_task_count', 'desc');
        } elseif ($this->orderby === 'join_task_count_asc') {
            $driver_list_object->orderBy('join_task_count', 'asc');
        } else {
            $driver_list_object->orderBy('id', 'desc');
        }

        $driver_list = $driver_list_object->get();

        // logger($driver_list->toArray());
        // exit;

        return $driver_list;
    }
    public function title(): string
    {
        return 'driver';
    }

    public function headings(): array
    {
        return [
            'id',
            '名前',
            '名前(カナ)',
            'email',
            '生年月日',
            '性別',
            '住所1',
            '住所2',
            '住所3',
            '住所4',
            '電話番号',
            '経歴',
            '紹介',
            '平均評価',
            '稼働数',
            '登録済み営業所一覧',
            '作成日',
            '更新日',
            '削除日',
        ];
    }
    public function map($row): array
    {
        return [
            $row->id,
            $row->full_name,
            $row->full_name_kana,
            $row->email,
            $row->birthday,
            $row->joinGender->name,
            $row->joinAddr1->name,
            $row->addr2,
            $row->addr3,
            $row->addr4,
            "TEL: " . $row->tel,
            $row->career,
            $row->introduction,
            $row->join_driver_review_avg_score,
            $row->join_task_count,
            $row->register_office_name,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
        ];
    }
}
