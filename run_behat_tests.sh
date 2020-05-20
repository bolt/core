#!/usr/bin/env bash

echo "Adding chromedriver to PATH variable"
PATH="$(pwd)/vendor/bin:${PATH}"

echo $PATH