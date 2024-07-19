#!/bin/bash
if [ -f database.sqlite ]; then
  echo "database.sqlite exists, skipping migration..."
else
  touch database.sqlite
  php migrate.php
fi
