#!/usr/bin/env bash

echo "Adding chromedriver to PATH variable"
export PATH="$(pwd)/vendor/bin:${PATH}"

echo $PATH