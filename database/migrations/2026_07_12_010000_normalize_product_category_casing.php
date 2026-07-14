<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NormalizeProductCategoryCasing extends Migration
{
    
    public function up(): void
    {
        $products = DB::table('products')->select('id', 'category')->get();

       
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
