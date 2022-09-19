<?php namespace IceCollection\News\Updates;

use IceCollection\News\Models\Category;
use October\Rain\Database\Updates\Seeder;

class SeedAllTables extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => trans('icecollection.news::lang.categories.uncategorized'),
            'slug' => 'uncategorized',
        ]);
    }
}
