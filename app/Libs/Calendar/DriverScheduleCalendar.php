<?php

namespace App\Libs\Calendar;

use Illuminate\Support\Facades\Log;

/**
 * カレンダー
 */
class DriverScheduleCalendar extends BaseCalendar
{
  public $prev;
  public $next;
  public $year_month; //受け取った日付をformatした文字列
  protected $calendar_month_datetime; //受け取った日付のDateTime
  public $schedule_list; // ドライバーのスケジュール
  public $task_list; // ドライバーの稼働

  public function __construct($calendar_month, $schedule_list, $task_list)
  {
    parent::__construct($calendar_month);
    $this->schedule_list = $schedule_list; 
    $this->task_list = $task_list; 
  }

  /**
   * 先月のリンク作成
   */
  protected function create_prev_link()
  {
    $dt = clone $this->this_month;
    return $dt->modify('-1 month')->format('Y-m');
  }

  /**
   * 翌月のリンク作成
   */
  protected function create_next_link()
  {
    $dt = clone $this->this_month;
    return $dt->modify('+1 month')->format('Y-m');
  }

  /**
   * カレンダー表示
   */
  public function show()
  {
    parent::show();
  }

  /**
   * カレンダーの先月の部分のhtml
   */
  public function get_tail()
  {
    $tail = '';
    $last_day_of_prev_month = new \DateTime('last day of' . $this->year_month . '-1 month');
    
    while ($last_day_of_prev_month->format('w') < 6) {
      $tail = sprintf('<td class="gray"><span>%s</span></td>', $last_day_of_prev_month->format('j')) . $tail;
      $last_day_of_prev_month->sub(new \DateInterval('P1D')); //マイナス1日
    }
    return $tail;
  }

  /**
   * カレンダーの当月の部分のhtml
   */
  public function get_body()
  {
    $body = "";
    //DatePeriodは日付の期間を取得
    $period = new \DatePeriod(
      new \DateTime('First day of' . $this->year_month), //当月1日から
      new \DateInterval('P1D'), //日付の間隔
      new \DateTime('First day of' . $this->year_month . '+1 month') //翌月1日まで
    );

    $today = new \DateTime('today');

    //日曜の場合はタグ開始
    foreach ($period as $day) {
      if ($day->format('w') == 0) {
        $body .= '<tr class="el_date">';
      }

      $today_class = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';

      // 稼働可能日 (シフト)
      $available_date_class = '';
      $available_date_html = '';
      foreach ($this->schedule_list as $item) {
        if ($day->format('Y-m-d') === $item->available_date) {
          $available_date_class = 'available_date';
          $route = route('driver.driver_schedule.destroy', ['schedule_id' => $item->id]);
          $available_date_html = "<button data-action='{$route}' class='{$available_date_class} js_available_date' data-available_date = '{$item->available_date}'>稼働可能日</button>";
          break;
        }
      }

      // 稼働日
      $task_date_class = '';
      $task_date_html = '';
      foreach ($this->task_list as $item) {
        if ($day->format('Y-m-d') === $item->task_date) {
          $task_date_class = 'task_date';
          $route = route('driver.driver_task.show', ['task_id' => $item->id]);
          $task_date_html = "<a href='{$route}' class='{$task_date_class}'> {$item->joinOffice->name}</a>";
          break;
        }
      }


      // html生成
      $body .= "<td data-day={$day->format('Y-m-d')} class='youbi_{$day->format('w')} {$today_class} el_hover'>
      <span>{$day->format('j')}</span>
      {$available_date_html}
      {$task_date_html}
      </td>";

      //土曜の場合はタグを閉じる
      if ($day->format('w') == 6) {
        $body .= '</tr>';
      }
    }

    return $body;
  }

  /**
   * カレンダーの翌月の部分のhtml
   */
  public function get_head()
  {
    $head = '';
    $first_day_of_next_month = new \DateTime('first day of' . $this->year_month . '+1 month'); //翌月1日
    while ($first_day_of_next_month->format('w') > 0) {
      $head .= sprintf('<td class="gray"><span>%s</span></td>', $first_day_of_next_month->format('j') );
      $first_day_of_next_month->add(new \DateInterval('P1D')); //日付を1日進める。
    }
    return $head;
  }
}
