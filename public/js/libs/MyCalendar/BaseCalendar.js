/**
 *カレンダークラス
 */
class Calendar {
    constructor(date) {
        if (date == undefined) {
            this.this_month = new Date(); //受け取った日付のDate
        } else {
            this.this_month = new Date(date); //受け取った日付のDate
        }
        this.year = this.this_month.getFullYear(); // 年
        this.month = this.this_month.getMonth() + 1; // 月
        this.day = this.this_month.getDate(); // 日
        // Y-m-d の形
        this.yearMonth = `${this.year}-${("00" + this.month).slice(-2)}-${("00" + this.day
        ).slice(-2)}`; //受け取った日付をformatした文字列
    }

    show() {
        let tail = this.get_tail();
        let body = this.get_body();
        let head = this.get_head();
        let html = `
        <tbody id='${this.year}-${this.month}' class="calendar_tbody">
         <tr class='el_day_week'>
          <td>Sun</td>
          <td>Mon</td>
          <td>Tue</td>
          <td>Wed</td>
          <td>Thu</td>
          <td>Fri</td>
          <td>Sat</td>
         </tr>
         <tr class='el_date'>
          ${tail}${body}${head}
         </tr>
        </tbody>`;

        let $body = document.querySelector(".calendar");
        //console.log(html);
        //console.log($body);
        $body.innerHTML += html;
    }

    /**
     * 先月の末部分
     */
    get_tail() {
        let tail = "";
        let this_month_copy = new Date(this.this_month.getTime()); // 指定された月 値渡し

        // 先月の最終日
        let last_day_of_prev_month = new Date(
            this_month_copy.getFullYear(),
            this_month_copy.getMonth(),
            0
        );
        // console.log(last_day_of_prev_month);

        while (last_day_of_prev_month.getDay() < 6) {
            tail =
                `<td class='gray'><span>${last_day_of_prev_month.getDate()}</span></td>` +
                tail;

            last_day_of_prev_month.setDate(
                last_day_of_prev_month.getDate() - 1
            ); //マイナス1日
        }
        return tail;
    }

    /**
     * this月のボディ
     */
    get_body() {
        let body = "";
        let this_month = new Date(this.this_month.getTime());

        // this月の初日
        let first_day_of_this_month = new Date(
            this_month.getFullYear(),
            this_month.getMonth(),
            1
        );

        // this月の最終日
        let last_day_of_this_month = new Date(
            this_month.getFullYear(),
            this_month.getMonth() + 1,
            0
        );

        let last_day = last_day_of_this_month.getDate(); //this月の最終日

        // this月の初日から最終日を生成
        for (let i = 1; i <= last_day; i++) {
            // 日曜日の場合はタグ開始
            if (first_day_of_this_month.getDay() == 0) {
                body += `<tr class='el_date'>`;
            }

            /* カレンダーの今日の部分に"today"クラス追加 */
            let todayClass = "";
            let today = new Date();
            let today_format = `${today.getFullYear()}-${
                today.getMonth() + 1
            }-${today.getDate()}`;
            let calendar_format = `${first_day_of_this_month.getFullYear()}-${
                first_day_of_this_month.getMonth() + 1
            }-${first_day_of_this_month.getDate()}`;
            if (today_format == calendar_format) {
                todayClass = "today";
            }

            /* idで日付を示す。Y-m-d 形式 */
            let id = `${first_day_of_this_month.getFullYear()}-${String(
                first_day_of_this_month.getMonth() + 1
            ).padStart(2, "0")}-${String(
                first_day_of_this_month.getDate()
            ).padStart(2, "0")}`;

            body += `<td id="${id}" class="youbi_${first_day_of_this_month.getDay()} ${todayClass} js_calendar_td el_pointer el_hover"><span>${i}</span></td>`;

            //土曜の場合はタグを閉じる
            if (first_day_of_this_month.getDay() == 6) {
                body += `</tr>`;
            }

            // プラス1日
            first_day_of_this_month.setDate(
                first_day_of_this_month.getDate() + 1
            );
        }

        //console.log(first_day_of_this_month);
        return body;
    }

    /**
     * 翌月の先頭部分
     */
    get_head() {
        let head = "";
        let this_month_copy = new Date(this.this_month.getTime()); // 指定された月 値渡し

        // 翌月の初日
        let first_day_of_prev_month = new Date(
            this_month_copy.getFullYear(),
            this_month_copy.getMonth() + 1,
            1
        );
        // console.log(first_day_of_prev_month);
        while (first_day_of_prev_month.getDay() > 0) {
            head += `<td class='gray'><span>${first_day_of_prev_month.getDate()}</span></td>`;
            first_day_of_prev_month.setDate(
                first_day_of_prev_month.getDate() + 1
            ); // プラス1日
        }
        return head;
    }
}

/**
 * カレンダーの制御
 * 自身で使うときはこちらだけインスタンス化する。
 */
class CalendarControl {
    constructor(from = 0, to = 12) {
        if (from >= 1 || to <= 0) {
            console.error(
                `引数の範囲が間違っています。「from <= 0 && to >=1」を満たしていません`
            );
            return;
        }
        // カレンダーインスタンス化用
        this.date = new Date();

        // 複数のカレンダーインスタンス化
        for (var i = from; i < to; i++) {
            let date_copy = new Date(this.date.getTime()); // 値渡しでインスタンス化
            date_copy.setMonth(this.date.getMonth() + i); // 次の月のDate生成
            let cal = new Calendar(date_copy.getTime()); // カレンダーインスタンス化
            cal.show();
        }

        this.this_date = new Date(); // 現在表示中のカレンダーのDate

        // 要素
        this.$next = document.getElementById("next"); // 次へ
        this.$prev = document.getElementById("prev"); // 前へ
        this.$today_btn = document.getElementById("today_btn"); // 今日ボタン

        // 初期状態のカレンダーをactive
        this.is_active_calendar();

        // 初期状態のカレンダーの年月テキストを設定
        this.calendar_year_month_text();

        // 初期状態のnext prevの表示
        this.show_next_prev();

        /* イベントリスナー */
        /* Next クリック */
        this.$next.addEventListener("click", () => {
            this.this_date.setMonth(this.this_date.getMonth() + 1); // 翌月に変更

            this.is_active_calendar();
            this.calendar_year_month_text();
            this.show_next_prev();
        });

        /* Prev クリック */
        this.$prev.addEventListener("click", () => {
            this.this_date.setMonth(this.this_date.getMonth() - 1); // 先月に変更

            this.is_active_calendar();
            this.calendar_year_month_text();
            this.show_next_prev();
        });

        /* Today クリック */
        this.$today_btn.addEventListener("click", () => {
            let today_date = new Date();
            this.this_date.setFullYear(today_date.getFullYear()); // 今年に変更
            this.this_date.setMonth(today_date.getMonth()); // 今月に変更

            this.is_active_calendar();
            this.calendar_year_month_text();
            this.show_next_prev();
        });
    }

    /*
     * カレンダーの next prev の表示非表示切り替え処理
     */
    show_next_prev() {
        // 二つ先のカレンダーが存在しない場合は nextボタンを非表示
        let next_date = new Date(this.this_date.getTime());
        next_date.setMonth(next_date.getMonth() + 2);
        let next_year_month = `${next_date.getFullYear()}-${
            next_date.getMonth() + 1
        }`; // Y-m の形式

        let $calendar_next = document.getElementById(`${next_year_month}`); // カレンダー取得
        if (!$calendar_next) {
            next.classList.remove("active");
        } else {
            next.classList.add("active");
        }

        // 二つ前のカレンダーが存在しない場合は prevボタンを非表示
        let prev_date = new Date(this.this_date.getTime());

        prev_date.setMonth(prev_date.getMonth() - 1);
        let prev_year_month = `${prev_date.getFullYear()}-${
            prev_date.getMonth() + 1
        }`; // Y-m の形式

        let $calendar_prev = document.getElementById(`${prev_year_month}`); // カレンダー取得
        if (!$calendar_prev) {
            prev.classList.remove("active");
        } else {
            prev.classList.add("active");
        }
    }

    /**
     * カレンダーの 「年 月」 テキスト部分 処理
     */
    calendar_year_month_text() {
        // 要素取得
        let $calendar_year_month = document.querySelector(
            ".calendar_year_month"
        );

        // テキストを生成
        let calendar_year_month_text = `${this.this_date.getFullYear()}年 ${
            this.this_date.getMonth() + 1
        }月`;

        // テキスト変更
        $calendar_year_month.textContent = calendar_year_month_text;
    }

    is_active_calendar() {
        let year_month = `${this.this_date.getFullYear()}-${
            this.this_date.getMonth() + 1
        }`; // Y-m の形式
        let $calendar = document.getElementById(`${year_month}`); // カレンダー取得

        // 非表示になっているものを含む 全てのカレンダー の 要素
        let $calendar_list = document.querySelectorAll(".calendar_tbody");

        // 全てのカレンダー非表示
        $calendar_list.forEach(($item) => {
            $item.classList.remove("active");
        });

        // 次に表示するカレンダーをアクティブにする
        $calendar.classList.add("active");
    }
}
