# Brilliance (Tutor-Parent Connector)

This project connects parents with tutors (marketplace). These instructions help you run DB migrations and seed sample data locally.

Prerequisites
- PHP CLI (7.4+), MySQL, and a webserver (WAMP/XAMPP)

Create the database (if not present):

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS brilliance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Import SQL migrations (from project root). Run each file or use the supplied PHP seeder:

Option A — import with mysql (manual):

```bash
mysql -u root -p brilliance < db/create_users.sql
mysql -u root -p brilliance < db/create_subjects.sql
mysql -u root -p brilliance < db/create_tutor_profile.sql
mysql -u root -p brilliance < db/create_parent_profile.sql
mysql -u root -p brilliance < db/create_bookings.sql
mysql -u root -p brilliance < db/seed_subjects.sql
```

Option B — run the PHP seed script (recommended):

```bash
php db/seed_sample_data.php
```

The PHP seeder reads the SQL files and creates three sample accounts:
- admin@example.test (admin)
- alice.parent@example.test (parent)
- bob.tutor@example.test (tutor)

Default password for samples: `password123` (used only for local testing).

If you encounter a foreign-key error when updating/creating `parent_profile` (common if an earlier migration created a bad FK), run the fix script:

```bash
php db/fix_parent_profile_fk.php
```

This script will attempt to drop incorrect foreign keys on `parent_profile` and add the correct constraint to `users(id)`. Inspect output and values in `parent_profile.user_id` if it reports problems.

Next steps
- Start the app in your local webserver and verify login/registration flows.
- After seeding, you can log in as the sample users and complete profiles where required.
