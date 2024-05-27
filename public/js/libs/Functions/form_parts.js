document.addEventListener("DOMContentLoaded", function () {
    window.globalFunction = {}; // 外部ファイルから呼び出せる様にグローバルに定義

    (function () {
        /**
         * formをsubmitしたときに確認ダイアログを表示する
         */
        let $form_list = document.querySelectorAll("form.js_confirm");
        let $loading = document.querySelector(".js_loading"); // ローディング

        if ($form_list) {
            form_parts();
        }

        function form_parts() {
            $form_list.forEach(($form) => {
                $form.addEventListener("submit", (e) => {
                    e.preventDefault();

                    let $submit = e.target.querySelector(
                        "input[type='submit']"
                    );

                    let msg = "";
                    if ($form.dataset.confirm_msg) {
                        msg = $form.dataset.confirm_msg;
                    } else {
                        msg = `「${$submit.value}」 こちらを行いますか？`;
                    }

                    let result = window.confirm(msg);

                    if (result) {
                        if ($loading) {
                            // ローディングが存在すれば実行
                            $loading.classList.add("active");
                        }
                        $form.submit();
                    }
                });
            });
        }
    })();

    (function () {
        /**
         * フォームの開閉 アコーディオン的
         */
        let $show_form_btn = document.querySelector(".js_show_form_btn"); // 開閉ボタン
        let $form = document.querySelector(".js_form"); // form要素

        if ($show_form_btn) {
            form_accordion();
        }
        function form_accordion() {
            $show_form_btn.addEventListener("click", (e) => {
                e.preventDefault();
                if ($form.classList.contains("js_active")) {
                    $form.classList.remove("js_active");
                    $show_form_btn.classList.remove("js_active");
                } else {
                    $form.classList.add("js_active");
                    $show_form_btn.classList.add("js_active");
                }
            });
        }
    })();

    (function () {
        /**
         * フォームリセット
         */
        let $input_text_list = document.querySelectorAll(
            "input[type='text'], input[type='date'], input[type='number'],input[type='hidden'] , textarea"
        );
        let $input_check_list = document.querySelectorAll(
            "input[type='checkbox']"
        );
        let $option_list = document.querySelectorAll("option");
        let $reset_form_btn = document.querySelector(".js_reset_form_btn"); // リセットボタン

        if ($reset_form_btn) {
            form_reset();
        }

        function form_reset() {
            $reset_form_btn.addEventListener("click", (e) => {
                e.preventDefault();
                $input_text_list.forEach(($input) => {
                    $input.value = "";
                });
                $input_check_list.forEach(($check) => {
                    $check.checked = false;
                });
                $option_list.forEach(($option) => {
                    $option.selected = false;
                });
            });
        }
    })();

    (function () {
        window.globalFunction.check_all = check_all; // 外部ファイルから呼び出せる様にグローバルに入れる
        window.globalFunction.uncheck_all = uncheck_all; // 外部ファイルから呼び出せる様にグローバルに入れる
        /**
         * チェクボックス 全チェック機能。
         *
         * @param $check_btn ボタン
         * @param $checkbox_list チェックボックスのリスト
         */
        function check_all($check_btn, $checkbox_list) {
            $check_btn.addEventListener("click", (e) => {
                e.preventDefault();
                $checkbox_list.forEach(($checkbox) => {
                    $checkbox.checked = true;
                });
            });
        }

        /**
         * チェクボックス 全チェック解除機能。
         *
         * @param $check_btn ボタン
         * @param $checkbox_list チェックボックスのリスト
         */
        function uncheck_all($check_btn, $checkbox_list) {
            $check_btn.addEventListener("click", (e) => {
                e.preventDefault();
                $checkbox_list.forEach(($checkbox) => {
                    $checkbox.checked = false;
                });
            });
        }
    })();

    /**
     * 配送会社IDが選択されている場合は、配送会社名の入力を無効化。
     * 配送会社IDが「所属なし」の場合は、配送会社名の入力を有効化。
     */
    (function () {
        if (document.querySelector(".js_form_delivery_company_name")) {
            let $form_delivery_company_name = document.querySelector(
                ".js_form_delivery_company_name"
            );

            let $delivery_company_id = "";
            let delivery_company_name = "";
            if (document.getElementById("delivery_company_name")) {
                $delivery_company_id = document.getElementById(
                    "delivery_company_id"
                );
                $delivery_company_name = document.getElementById(
                    "delivery_company_name"
                );
            } else if (document.getElementById("task_delivery_company_name")) {
                $delivery_company_id = document.getElementById(
                    "task_delivery_company_id"
                );
                $delivery_company_name = document.getElementById(
                    "task_delivery_company_name"
                );
            }

            check_company();

            $delivery_company_id.addEventListener("change", () => {
                check_company();
            });

            /**
             * 配送会社IDが入力されていたら非表示。所属無し(None)なら表示
             */
            function check_company() {
                if ($delivery_company_id.value === "None") {
                    $form_delivery_company_name.classList.add("js_active");
                } else {
                    $form_delivery_company_name.classList.remove("js_active");
                }
            }
        }
    })();
});
