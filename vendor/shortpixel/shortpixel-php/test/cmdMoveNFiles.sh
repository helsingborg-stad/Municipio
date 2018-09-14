#!/bin/bash
find $1 -maxdepth 1 -type f |head -1000|xargs mv -t $2