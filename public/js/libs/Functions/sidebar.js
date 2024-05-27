/* メニュボタンをクリックして, サイドバーメニューの開閉 */
document.addEventListener("DOMContentLoaded", function () {
    let $menu_btn = document.querySelector(".js_menuBtn"); // メニューボタン
    let $menu = document.querySelector(".js_menu"); // サイドバーメニュー
    let $overlay = document.querySelector(".js_overlay"); //オーバーレイ
    let $html = document.querySelector("html"); // htmlタグ
    let scroll_position; //スクロール位置

    if ($menu_btn) {
        // open
        $menu_btn.addEventListener("click", () => {
            scroll_position = window.scrollY; //スクロール位置取得
            $html.classList.add("js_scroll_stop");
            $html.style.top = -scroll_position + "px";

            $menu.classList.add("active");
            $overlay.classList.add("active");
        });
    }

    // close
    if ($overlay) {
        $overlay.addEventListener("click", () => {
            $html.classList.remove("js_scroll_stop");
            $overlay.classList.remove("active");
            $menu.classList.remove("active");
            window.scrollTo(0, scroll_position); //スクロール位置戻す
        });
    }

    if ($menu_btn) {
        // スクロールすると、メニューボタンが画面固定される(アニメ)
        window.addEventListener("scroll", () => {
            if (window.scrollY >= 70) {
                $menu_btn.classList.add("scroll");
            } else {
                $menu_btn.classList.remove("scroll");
            }
        });
    }
});
