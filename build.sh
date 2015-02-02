#!/bin/bash

thrift -v --gen php:server,validate tutorial.thrift
thrift -v -o web --gen js:jquery tutorial.thrift
