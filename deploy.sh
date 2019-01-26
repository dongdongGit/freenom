#!/bin/sh

# update source code
git checkout .
git fetch origin master
git merge origin/master