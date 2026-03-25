#!/usr/bin/env bash

runner="php"
yii="./yii"
log="upkeep.log"

source "$(dirname "${BASH_SOURCE[0]}")/config.shlib"

"$runner" "$yii" importance/recalculate >> "$log"
