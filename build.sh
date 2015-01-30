#!/bin/bash

thrift --gen php:server,validate tutorial.thrift
thrift -o web --gen js:jquery tutorial.thrift
