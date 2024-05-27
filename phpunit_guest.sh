#!/bin/bash

## テスト実行 ##
# ゲストやその他のテスト
./vendor/bin/phpunit tests/Unit/Api/Normal/Guest

# #テスト終了後ファイル名を変更
DATE=$(date '+%Y-%m-%d_%H_%M_%S')
OLD_FILE="./storage/private/unit_test/unit_test_evidence_guest_api.csv"
NEW_FILE="./storage/private/unit_test/unit_test_normal_evidence_guest_api_${DATE}.csv"

mv "$OLD_FILE" "$NEW_FILE"

echo "${NEW_FILE} を作成しました。"
