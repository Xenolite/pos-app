<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NormalizeProductCategoryCasing extends Migration
{
    /**
     * "Minuman", "minuman", and "MINUMAN" were previously stored as 3
     * separate categories because the category field was free text with
     * no normalization. This one-time cleanup merges them into a single
     * canonical Title Case value (e.g. "Minuman"), matching the
     * normalization now applied on every new save in
     * ProductController@store / @update.
     */
    public function up(): void
    {
        $products = DB::table('products')->select('id', 'category')->get();

        // Group by lowercased category so "Minuman"/"minuman"/"MINUMAN"
        // all resolve to the same canonical form.
        $canonical = [];

        foreach ($products as $product) {
            if (is_null($product->category) || trim($product->category) === '') {
                continue;
            }

            $key = Str::lower(trim($product->category));

            if (! isset($canonical[$key])) {
                $canonical[$key] = Str::title(trim($product->category));
            }
        }

        foreach ($products as $product) {
            if (is_null($product->category) || trim($product->category) === '') {
                continue;
            }

            $key = Str::lower(trim($product->category));
            $normalized = $canonical[$key];

            if ($product->category !== $normalized) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['category' => $normalized]);
            }
        }
    }

    /**
     * Not reversible — the original mixed casing is not preserved.
     */
    public function down(): void
    {
        //
    }
}
