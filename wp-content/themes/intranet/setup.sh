#!/bin/bash

echo "\033[35mThis guide will help you setup theme strings and variables correctly.\033[0m"

echo "\033[34m\033[1mEnter theme PHP namespace:\033[0m "
read theme_namespace
find ./ -type f ! -name "setup.sh" -exec sed -i '' -e "s/(#theme_namespace#)/$theme_namespace/g" {} >/dev/null 2>&1 \;

echo "\033[34m\033[1mEnter theme PHP namespace in CAPS:\033[0m "
read theme_namespace_caps
find ./ -type f ! -name "setup.sh" -exec sed -i '' -e "s/(#theme_namespace_caps#)/$theme_namespace_caps/g" {} >/dev/null 2>&1 \;
