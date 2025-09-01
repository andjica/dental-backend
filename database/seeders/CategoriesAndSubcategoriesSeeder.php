<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class CategoriesAndSubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Konzervativa' => [
                'Kompoziti',
                'Matrice, diskovi, stripse',
                'Ostali materijali',
                'Svrdla, gumice, kamenčići, četkice',
            ],
            'Endodoncija' => [
                'Endo instrumenti',
                'FANTA',
                'Pribor i materijali za endodonciju',
            ],
            'Otisne mase i žlice' => [],
            'Uređaji' => ['Eighteeth', 'FANTA'],
            'Instrumenti' => ['AR Instrumed', 'Hahnenkratt', 'Ogledalca i držači'],
            'Kolčići' => [],
            'Dezinfekcija i potrošni materijal' => ['Ostalo', 'Zaštitne maske', 'Zaštitne rukavice'],
            'Rasprodaja' => [],
        ];

        foreach ($data as $categoryName => $subcategories) {
            $category = Category::create(['name' => $categoryName]);

            foreach ($subcategories as $sub) {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $sub,
                ]);
            }
        }
    }
}
