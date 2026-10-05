#!/usr/bin/env bash
set -euo pipefail
python3 - <<'PYTHON'
import pathlib
import subprocess
files = [p for root in ('themes', 'plugins') for p in pathlib.Path(root).rglob('*.php')]
if not files:
    raise RuntimeError('No PHP source files found')
for source in files:
    subprocess.run(['php', '-l', str(source)], check=True)
print(f'PHP syntax checked: {len(files)} files')
PYTHON
php tests/dashboard-query.php
php tests/dashboard-deploy.php
npx playwright test tests/dashboard-preview.spec.js --workers=1
