Release checklist — Brilliance

1) Back up the database
   - Dump the current DB before applying migrations.

2) Apply database migrations
   - Option A (recommended): Log in as admin and open:
     http://localhost/brilliance/admin/run_migrations.php
   - Option B (CLI): enable `pdo_mysql` for PHP CLI then run:
     php scripts/apply_migrations.php
   - If migrations were previously locked, delete `db/migrations.lock` before running.

3) Reconcile qualification uploads
   - As admin (web): http://localhost/brilliance/scripts/run_reconcile.php
   - Or CLI: php scripts/reconcile_qualification_files.php

4) Verify admin pages
   - Open:
     - http://localhost/brilliance/admin/users.php
     - http://localhost/brilliance/admin/tutors.php
     - http://localhost/brilliance/admin/audit.php
   - Test promote/suspend/verify actions and confirm `admin_audit` entries appear.

5) QA critical flows
   - Register/login as parent and tutor
   - Book a tutor, accept/reject booking
   - Send/receive messages
   - Submit a review and check tutor rating updates
   - Upload qualification file as tutor and confirm it appears on public profile

6) Clean up
   - Remove any temporary scripts (e.g., `scripts/create_admin_via_web.php`)
   - Commit and tag the release

7) Post-release
   - Set up monitoring/logging
   - Schedule a full walkthrough and sign-off

Notes:
- If PHP CLI reports "could not find driver", enable `pdo_mysql` in the CLI php.ini (see docs).
- The migration runner tolerates duplicate-column/constraint warnings and will skip them.
