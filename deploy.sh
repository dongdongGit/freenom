#!/bin/sh

LOG_FILE="/home/wwwlogs/freenom_deploy.log"

if [ ! -f "$LOG_FILE"]; then
  touch "$LOG_FILE"
fi

date >> "$LOG_FILE"
echo "Start deployment" >>"$LOG_FILE"
# update source code
echo "pulling source code..." >> "$LOG_FILE"
git reset --hard origin/master
git clean -f
git pull origin master
echo "Finished." >>"$LOG_FILE"
echo >> $LOG_FILE
npm run production