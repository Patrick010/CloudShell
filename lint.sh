#!/bin/bash
set -e

echo "--- Installing PHP dependencies ---"
composer install

echo "--- Running PHPCS ---"
vendor/bin/phpcs --standard=PSR12 .

echo "--- Running PHP-CS-FIXER ---"
vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run --diff

echo "--- Running PHPStan ---"
vendor/bin/phpstan analyse . --level=5 # Level max is too strict for POC

echo "--- Running Psalm ---"
# Psalm needs to be initialized first
if [ ! -f psalm.xml ]; then
    vendor/bin/psalm --init
fi
vendor/bin/psalm

echo "--- Creating package.json ---"
cat <<EOF > package.json
{
  "devDependencies": {
    "eslint": "^8.0.0",
    "prettier": "^2.0.0"
  }
}
EOF

echo "--- Installing JS dependencies ---"
npm install

echo "--- Running ESLint ---"
./node_modules/.bin/eslint js/

echo "--- Running Prettier ---"
./node_modules/.bin/prettier --write js/

echo "--- Linting and Hardening Complete ---"
