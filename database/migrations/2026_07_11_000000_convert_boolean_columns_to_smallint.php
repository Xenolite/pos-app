<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Laravel always sends PHP booleans to the database as integers
     * (0 / 1) in its query bindings — this is true for every driver,
     * not just Postgres. MySQL/SQLite silently accept that because
     * their "boolean" is really just a tinyint under the hood.
     *
     * Real Postgres `boolean` columns don't implicitly cast an
     * integer literal, so every write to is_online / dark_mode throws:
     *   "column is of type boolean but expression is of type integer"
     *
     * Switching these columns to smallint sidesteps the problem
     * entirely: integers go in cleanly, and the `casts()` boolean
     * cast on the User model still converts 0/1 back to true/false
     * automatically when you read the attribute in PHP.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN is_online DROP DEFAULT');
        DB::statement('ALTER TABLE users ALTER COLUMN is_online TYPE smallint USING (is_online::int)');
        DB::statement('ALTER TABLE users ALTER COLUMN is_online SET DEFAULT 0');

        DB::statement('ALTER TABLE users ALTER COLUMN dark_mode DROP DEFAULT');
        DB::statement('ALTER TABLE users ALTER COLUMN dark_mode TYPE smallint USING (dark_mode::int)');
        DB::statement('ALTER TABLE users ALTER COLUMN dark_mode SET DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN is_online DROP DEFAULT');
        DB::statement('ALTER TABLE users ALTER COLUMN is_online TYPE boolean USING (is_online::int::boolean)');
        DB::statement('ALTER TABLE users ALTER COLUMN is_online SET DEFAULT false');

        DB::statement('ALTER TABLE users ALTER COLUMN dark_mode DROP DEFAULT');
        DB::statement('ALTER TABLE users ALTER COLUMN dark_mode TYPE boolean USING (dark_mode::int::boolean)');
        DB::statement('ALTER TABLE users ALTER COLUMN dark_mode SET DEFAULT false');
    }
};
