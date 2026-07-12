<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Same root cause and same fix as the users table migration:
     * Laravel always sends PHP booleans to the database as integers,
     * which real Postgres `boolean` columns don't implicitly accept.
     *
     * On top of that, Product.php was working around it with a custom
     * setAttribute() that force-wrote the string literals 'true'/'false'
     * — which broke Eloquent's dirty-checking entirely, because PHP casts
     * the non-empty string 'false' to boolean true. That's why toggling
     * is_active appeared to do nothing: Eloquent thought nothing changed
     * and silently dropped the column from every UPDATE query.
     *
     * Converting to smallint removes the need for that workaround
     * completely — the `casts()` boolean cast on the model still makes
     * is_active/price_after_tax read as true/false in PHP as normal.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE products ALTER COLUMN is_active DROP DEFAULT');
        DB::statement('ALTER TABLE products ALTER COLUMN is_active TYPE smallint USING (is_active::int)');
        DB::statement('ALTER TABLE products ALTER COLUMN is_active SET DEFAULT 1');

        DB::statement('ALTER TABLE products ALTER COLUMN price_after_tax DROP DEFAULT');
        DB::statement('ALTER TABLE products ALTER COLUMN price_after_tax TYPE smallint USING (price_after_tax::int)');
        DB::statement('ALTER TABLE products ALTER COLUMN price_after_tax SET DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products ALTER COLUMN is_active DROP DEFAULT');
        DB::statement('ALTER TABLE products ALTER COLUMN is_active TYPE boolean USING (is_active::int::boolean)');
        DB::statement('ALTER TABLE products ALTER COLUMN is_active SET DEFAULT true');

        DB::statement('ALTER TABLE products ALTER COLUMN price_after_tax DROP DEFAULT');
        DB::statement('ALTER TABLE products ALTER COLUMN price_after_tax TYPE boolean USING (price_after_tax::int::boolean)');
        DB::statement('ALTER TABLE products ALTER COLUMN price_after_tax SET DEFAULT false');
    }
};
