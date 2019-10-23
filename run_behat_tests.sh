#!/usr/bin/env bash

if [[ "$OSTYPE" == "linux-gnu" ]]; then

        echo "linux";
elif [[ "$OSTYPE" == "darwin"* ]]; then
        wget http://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar -P ./bin

        wget https://chromedriver.storage.googleapis.com/77.0.3865.40/chromedriver_mac64.zip -P ./bin
        unzip ./bin/chromedriver_mac64.zip -d ./bin
        rm ./bin/chromedriver_mac64.zip
        java -jar -Dwebdriver.chrome.driver="./bin/chromedriver" ./bin/selenium-server-standalone-3.141.59.jar

elif [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
        echo "windows";
else
        echo "Don't know how to handle this platform, sorry";
fi