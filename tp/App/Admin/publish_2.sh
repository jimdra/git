#!/bin/bash

cd /data/
rsync -azv /data/www/html/landing  -e "ssh -i leif-key" 10.104.120.92:/data/www/html/landing
