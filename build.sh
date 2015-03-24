#!/bin/bash

THRIFT_FILE=$1

if [ -z $THRIFT_FILE ]; then
    echo Please, specify .thrift file
    exit 1
fi

thrift -v --gen php:server,validate $THRIFT_FILE
thrift -v -o web --gen js:jquery $THRIFT_FILE
