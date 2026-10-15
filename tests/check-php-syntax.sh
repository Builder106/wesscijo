#!/usr/bin/env bash
set -euo pipefail

python3 - <<'PYTHON'
import pathlib
import subprocess
import sys

files = sorted(list(pathlib.Path('themes').rglob('*.php')))
if not files:
    print('No PHP files found in themes/', file=sys.stderr)
    sys.exit(1)

for f in files:
    res = subprocess.run(['php', '-l', str(f)], capture_output=True, text=True)
    if res.returncode != 0:
        print(f"Error in {f}:\n{res.stderr or res.stdout}", file=sys.stderr)
        sys.exit(1)

print(f"PHP syntax checked: all {len(files)} theme files passed cleanly.")
PYTHON
