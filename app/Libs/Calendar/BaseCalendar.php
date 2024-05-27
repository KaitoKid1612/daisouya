<?php

namespace App\Libs\Calendar;

/**
 * カレンダー
 */
class BaseCalendar
{
  public $prev;
  public $next;
  public $year_month; //受け取った日付をformatした文字列
  protected $calendar_month_datetime; //受け取った日付のDateTime
  public $this_month;

  public function __construct($calendar_month)
  {
    try {
      if (!isset($calendar_month) || !preg_match('/\A\d{4}-\d{2}\z/', $calendar_month)) {
        throw new \Exception();
      }
      $this->this_month = new \DateTime($calendar_month);
    } catch (\Exception $e) {
      $this->this_month = new \DateTime('first day of this month');
    }
    $this->prev = $this->create_prev_link();
    $this->next = $this->create_next_link();
    $this->year_month = $this->this_month->format('F Y');
  }

  /**
   * 先月のリンク作成
   */
  protected function create_prev_link()
  {
    $dt = clone $this->this_month;
    //DateTime->modify()は日付の加算・減算する
    return $dt->modify('-1 month')->format('Y-m');
  }

  /**
   * 翌月のリンク作成
   */
  protected function create_next_link()
  {
    $dt = clone $this->this_month;
    //DateTime->modify()は日付の加算・減算する
    return $dt->modify('+1 month')->format('Y-m');
  }

  /**
   * カレンダー表示
   */
  public function show()
  {
    $tail = $this->get_tail();
    $body = $this->get_body();
    $head = $this->get_head();
    $html = '<tr class="el_date">' . $tail . $body . $head .  '</tr>'; //先月と今月と翌月をまとめる
    echo $html;
  }

  /**
   * カレンダーの先月の部分のhtml
   */
  public function get_tail()
  {
    $tail = '';
    $last_day_of_prev_month = new \DateTime('last day of' . $this->year_month . '-1 month');
    while ($last_day_of_prev_month->format('w') < 6) {
      $tail = sprintf('<td class="gray">%d</td>', $last_day_of_prev_month->format('d')) . $tail;
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
    foreach ($period as $day) {
      if ($day->format('w') == 0) {
        $body .= '<tr class="el_date">';
      } //日曜の場合はタグ
      $today_class = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';
      $body .= sprintf('<td class="youbi_%d %s">%d</td>', $day->format('w'), $today_class, $day->format('d'));
      if ($day->format('w') == 6) {
        $body .= '</tr>';
      } //土曜の場合はタグを閉じる
    }
    return $body;
  }

  /**
   * カレンダーの翌月の部分のhtml
   */
  public function get_head()
  {
    $head = '';
    $firstDayOfNextMonth = new \DateTime('first day of' . $this->year_month . '+1 month'); //翌月1日
    while ($firstDayOfNextMonth->format('w') > 0) {
      $head .= sprintf('<td class="gray">%d</td>', $firstDayOfNextMonth->format('d'));
      $firstDayOfNextMonth->add(new \DateInterval('P1D')); //日付を1日進める。
    }
    return $head;
  }
}
