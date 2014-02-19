#!/bin/bash

thrift --gen php:server tutorial.thrift
thrift -o web --gen js tutorial.thrift
composer update -o
