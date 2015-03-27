#!/usr/bin/env bash

mkdir build/

tar -zcvf build/build.tar.gz \
    app/ \
    bootstrap.php \
    cli-config.php \
    config/ \
    gen-php/ \
    src/ \
    vendor/ \
    web/
