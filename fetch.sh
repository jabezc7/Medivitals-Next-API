#!/bin/bash
read_var(){
  echo $(grep -v '^#' .env | grep -e "$1" | sed -e 's/.*=//')
}

read -p "AWS Profile [default]: " profile
profile=${profile:-default}

aws s3 --profile $profile cp s3://development-dumps/$(read_var "DB_DATABASE").sql database.sql

mysql -h 127.0.0.1 --port=$(read_var "FORWARD_DB_PORT") -u $(read_var "DB_USERNAME") -p$(read_var "DB_PASSWORD") $(read_var "DB_DATABASE") < database.sql

./vendor/bin/sail artisan --env=testing migrate:fresh
./vendor/bin/sail artisan --env=testing db:seed --class=SectionsSeeder
./vendor/bin/sail artisan --env=testing db:seed --class=TypesSeeder
./vendor/bin/sail artisan --env=testing db:seed --class=InitialSeeder
