#!/usr/bin/env bash

SCRIPT_DIR=$(dirname -- "$0");
MEDIAWIKI_DIR="${SCRIPT_DIR}"/../../../..

ARGS=()
for FILE in "${SCRIPT_DIR}"/*ParserTests.txt; do
	ARGS+=("--file=${FILE}");
done

php "${MEDIAWIKI_DIR}/tests/parser/parserTests.php" "${ARGS[@]}"
