#!/bin/bash

mv .env .env.backup

mv .env.staging .env

flyctl deploy

mv .env .env.staging

mv .env.backup .env