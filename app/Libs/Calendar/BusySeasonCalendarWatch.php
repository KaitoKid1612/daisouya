<?php

namespace App\Libs\Calendar;

use Illuminate\Support\Facades\Log;

/**
 * カレンダー
 */
class BusySeasonCalendarWatch extends BaseCalendar
{
  public $prev;
  public $next;
  public $year_month; //受け取った日付をformatした文字列
  protected $calendar_month_datetime; //受け取った日付のDateTime
  public $busy_date_list; // ドライバーのスケジュール

  public function __construct($calendar_month, $busy_date_list)
  {
    parent::__construct($calendar_month);
    $this->busy_date_list = $busy_date_list; // ドライバーのスケジュール

    $date =  new \Datetime();
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
      $busy_date_class = '';
      $busy_date_html = '';
      foreach ($this->busy_date_list as $item) {
        if ($day->format('Y-m-d') === $item->busy_date) {
          $busy_date_class = 'busy_date';
          $route = route('admin.web_busy_season.destroy', ['busy_season_id' => $item->id]);
          $busy_date_html = "<button data-action='{$route}' class='{$busy_date_class} el_black js_busy_date' data-busy_date = '{$item->busy_date}'>繁忙</button>";
          break;
        }
      }

      // カレンダーの日付ごとのUI
      $text = $busy_date_html;

      // html生成
      $body .= "<td data-day={$day->format('Y-m-d')} class='youbi_{$day->format('w')} {$today_class} el_relative'>
      <span>{$day->format('j')}</span>
      {$text}
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
      $head .= sprintf('<td class="gray"><span>%s</span></td>', $first_day_of_next_month->format('j'));
      $first_day_of_next_month->add(new \DateInterval('P1D')); //日付を1日進める。
    }
    return $head;
  }
}
