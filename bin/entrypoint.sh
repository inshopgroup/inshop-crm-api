#!/usr/bin/env bash

crontab -u www-data ./crontab && /usr/bin/supervisord
