#!/bin/bash

# Clears all cached data for development
echo -e \\n"> rm -rf /dev/shm/project/cache/dev/"
rm -rf /dev/shm/project/cache/dev/
echo -e \\n"> rm -rf /dev/shm/project/cache/de_/"
rm -rf /dev/shm/project/cache/de_/
echo -e \\n"> rm -rf app/cache/prod"
rm -rf app/cache/prod/
echo -e \\n"> redis-cli flushall"
redis-cli flushall
