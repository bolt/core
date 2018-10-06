#!/usr/bin/env bash

rm -rf vendor
mkdir -p vendor/

wget https://github.com/sparksuite/simplemde-markdown-editor/archive/1.11.2.tar.gz -O vendor/simplemde-markdown-editor.tgz
mkdir -p vendor/simplemde-markdown-editor
tar -xvzf vendor/simplemde-markdown-editor.tgz --strip-components=1 -C vendor/simplemde-markdown-editor

