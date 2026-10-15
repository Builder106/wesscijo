#!/usr/bin/env bash
# promote-to-fallback.sh
# Promotes the verified wesscijo theme to wessci-fallback as a stable recovery baseline.

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_THEME="${ROOT_DIR}/themes/wesscijo"
TARGET_THEME="${ROOT_DIR}/themes/wessci-fallback"

if [[ ! -d "${SOURCE_THEME}" ]]; then
	echo "Error: Source theme directory not found at ${SOURCE_THEME}" >&2
	exit 1
fi

echo "==> Promoting ${SOURCE_THEME} to ${TARGET_THEME}..."
mkdir -p "${TARGET_THEME}"

# Sync all theme assets and templates, excluding OS metadata
rsync -a --delete --exclude='.DS_Store' "${SOURCE_THEME}/" "${TARGET_THEME}/"

# Update style.css theme header
python3 - <<'PYTHON'
import pathlib
import re

style_path = pathlib.Path("themes/wessci-fallback/style.css")
content = style_path.read_text(encoding="utf-8")

# Replace header block
header_pattern = r"/\*[\s\S]*?\*/"
fallback_header = """/*
Theme Name: WesSciJo Fallback
Theme URI: https://wesleyan.edu/
Author: Olayinka Vaughan
Description: A white and red journal theme with section dropdowns, illustrated articles, and an editorial masthead.
Version: 1.0.0
License: GNU General Public License v2 or later
Text Domain: wessci-fallback
*/"""

new_content = re.sub(header_pattern, fallback_header, content, count=1)
style_path.write_text(new_content, encoding="utf-8")
print("Updated themes/wessci-fallback/style.css header.")
PYTHON

# Update functions.php style handle
python3 - <<'PYTHON'
import pathlib

functions_path = pathlib.Path("themes/wessci-fallback/functions.php")
content = functions_path.read_text(encoding="utf-8")
new_content = content.replace("wp_enqueue_style(\n\t\t'wesscijo',", "wp_enqueue_style(\n\t\t'wessci-fallback',")
new_content = new_content.replace("wp_enqueue_style( 'wesscijo',", "wp_enqueue_style( 'wessci-fallback',")
functions_path.write_text(new_content, encoding="utf-8")
print("Updated themes/wessci-fallback/functions.php stylesheet enqueue handle.")
PYTHON

# Verify PHP syntax on all target PHP files if php CLI is available
if command -v php >/dev/null 2>&1; then
	echo "==> Checking PHP syntax across fallback templates..."
	for php_file in "${TARGET_THEME}"/*.php; do
		php -l "${php_file}" >/dev/null
	done
	echo "PHP syntax verified."
else
	echo "Note: php CLI not installed locally. Run verification suite on ampere-dev."
fi

echo "==> Successfully promoted wesscijo to wessci-fallback."
