#!/usr/bin/env bash

[[ ":${PATH}:" != *":$(pwd)/vendor/bin:"* ]] && PATH="$(pwd)/vendor/bin:${PATH}"

echo $PATH