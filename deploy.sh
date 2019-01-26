#!/bin/sh

# update source code
if [! -f "test.log"]; then 
touch test.log
fi
who >> test.log
git pull