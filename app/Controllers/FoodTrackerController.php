<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class FoodTrackerController extends Controller
{
    public function index(): void
    {
        View::render('calculators/food-tracker', [
            'title' => __('nav.food_tracker'),
            'foods' => self::getFoodDatabase(),
        ]);
    }

    public static function getFoodDatabase(): array
    {
        return [
            // GREEN - Low carb / high protein / nutrient dense
            ['name' => 'Chicken Breast (4 oz)', 'calories' => 165, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Turkey Breast (4 oz)', 'calories' => 153, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Salmon (4 oz)', 'calories' => 208, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Tuna (4 oz)', 'calories' => 132, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Shrimp (4 oz)', 'calories' => 120, 'carbs' => 1, 'category' => 'Green'],
            ['name' => 'Tilapia (4 oz)', 'calories' => 110, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Cod (4 oz)', 'calories' => 93, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Egg (whole)', 'calories' => 72, 'carbs' => 0.4, 'category' => 'Green'],
            ['name' => 'Egg Whites (3)', 'calories' => 51, 'carbs' => 0.7, 'category' => 'Green'],
            ['name' => 'Greek Yogurt, Plain (cup)', 'calories' => 100, 'carbs' => 6, 'category' => 'Green'],
            ['name' => 'Cottage Cheese, Low-fat (cup)', 'calories' => 163, 'carbs' => 6, 'category' => 'Green'],
            ['name' => 'Broccoli (cup)', 'calories' => 55, 'carbs' => 11, 'category' => 'Green'],
            ['name' => 'Spinach (cup, raw)', 'calories' => 7, 'carbs' => 1, 'category' => 'Green'],
            ['name' => 'Kale (cup, raw)', 'calories' => 33, 'carbs' => 6, 'category' => 'Green'],
            ['name' => 'Cauliflower (cup)', 'calories' => 27, 'carbs' => 5, 'category' => 'Green'],
            ['name' => 'Asparagus (cup)', 'calories' => 27, 'carbs' => 5, 'category' => 'Green'],
            ['name' => 'Green Beans (cup)', 'calories' => 31, 'carbs' => 7, 'category' => 'Green'],
            ['name' => 'Zucchini (cup)', 'calories' => 17, 'carbs' => 3, 'category' => 'Green'],
            ['name' => 'Cucumber (cup)', 'calories' => 16, 'carbs' => 4, 'category' => 'Green'],
            ['name' => 'Bell Pepper (medium)', 'calories' => 31, 'carbs' => 6, 'category' => 'Green'],
            ['name' => 'Tomato (medium)', 'calories' => 22, 'carbs' => 5, 'category' => 'Green'],
            ['name' => 'Celery (cup)', 'calories' => 14, 'carbs' => 3, 'category' => 'Green'],
            ['name' => 'Mushrooms (cup)', 'calories' => 15, 'carbs' => 2, 'category' => 'Green'],
            ['name' => 'Lettuce, Romaine (cup)', 'calories' => 8, 'carbs' => 1.5, 'category' => 'Green'],
            ['name' => 'Cabbage (cup)', 'calories' => 22, 'carbs' => 5, 'category' => 'Green'],
            ['name' => 'Brussels Sprouts (cup)', 'calories' => 56, 'carbs' => 11, 'category' => 'Green'],
            ['name' => 'Avocado (half)', 'calories' => 161, 'carbs' => 9, 'category' => 'Green'],
            ['name' => 'Almonds (1 oz)', 'calories' => 164, 'carbs' => 6, 'category' => 'Green'],
            ['name' => 'Walnuts (1 oz)', 'calories' => 185, 'carbs' => 4, 'category' => 'Green'],
            ['name' => 'Olive Oil (tbsp)', 'calories' => 119, 'carbs' => 0, 'category' => 'Green'],
            ['name' => 'Strawberries (cup)', 'calories' => 49, 'carbs' => 12, 'category' => 'Green'],
            ['name' => 'Blueberries (cup)', 'calories' => 85, 'carbs' => 21, 'category' => 'Green'],
            ['name' => 'Raspberries (cup)', 'calories' => 64, 'carbs' => 15, 'category' => 'Green'],
            ['name' => 'Tofu, Firm (4 oz)', 'calories' => 88, 'carbs' => 2, 'category' => 'Green'],
            ['name' => 'Lean Ground Turkey (4 oz)', 'calories' => 170, 'carbs' => 0, 'category' => 'Green'],

            // YELLOW - Moderate carbs / balanced
            ['name' => 'Oatmeal (cup, cooked)', 'calories' => 154, 'carbs' => 27, 'category' => 'Yellow'],
            ['name' => 'Brown Rice (cup)', 'calories' => 216, 'carbs' => 45, 'category' => 'Yellow'],
            ['name' => 'Quinoa (cup)', 'calories' => 222, 'carbs' => 39, 'category' => 'Yellow'],
            ['name' => 'Sweet Potato (medium)', 'calories' => 103, 'carbs' => 24, 'category' => 'Yellow'],
            ['name' => 'Potato, Baked (medium)', 'calories' => 161, 'carbs' => 37, 'category' => 'Yellow'],
            ['name' => 'Banana (medium)', 'calories' => 105, 'carbs' => 27, 'category' => 'Yellow'],
            ['name' => 'Apple (medium)', 'calories' => 95, 'carbs' => 25, 'category' => 'Yellow'],
            ['name' => 'Orange (medium)', 'calories' => 62, 'carbs' => 15, 'category' => 'Yellow'],
            ['name' => 'Grapes (cup)', 'calories' => 104, 'carbs' => 27, 'category' => 'Yellow'],
            ['name' => 'Pear (medium)', 'calories' => 101, 'carbs' => 27, 'category' => 'Yellow'],
            ['name' => 'Mango (cup)', 'calories' => 99, 'carbs' => 25, 'category' => 'Yellow'],
            ['name' => 'Pineapple (cup)', 'calories' => 82, 'carbs' => 22, 'category' => 'Yellow'],
            ['name' => 'Kiwi (medium)', 'calories' => 42, 'carbs' => 10, 'category' => 'Yellow'],
            ['name' => 'Whole Wheat Bread (slice)', 'calories' => 81, 'carbs' => 14, 'category' => 'Yellow'],
            ['name' => 'Whole Wheat Tortilla', 'calories' => 130, 'carbs' => 22, 'category' => 'Yellow'],
            ['name' => 'Whole Wheat Pasta (cup)', 'calories' => 174, 'carbs' => 37, 'category' => 'Yellow'],
            ['name' => 'Black Beans (cup)', 'calories' => 227, 'carbs' => 41, 'category' => 'Yellow'],
            ['name' => 'Lentils (cup)', 'calories' => 230, 'carbs' => 40, 'category' => 'Yellow'],
            ['name' => 'Chickpeas (cup)', 'calories' => 269, 'carbs' => 45, 'category' => 'Yellow'],
            ['name' => 'Kidney Beans (cup)', 'calories' => 225, 'carbs' => 40, 'category' => 'Yellow'],
            ['name' => 'Corn (cup)', 'calories' => 132, 'carbs' => 29, 'category' => 'Yellow'],
            ['name' => 'Peas (cup)', 'calories' => 118, 'carbs' => 21, 'category' => 'Yellow'],
            ['name' => 'Hummus (2 tbsp)', 'calories' => 70, 'carbs' => 6, 'category' => 'Yellow'],
            ['name' => 'Peanut Butter (2 tbsp)', 'calories' => 188, 'carbs' => 7, 'category' => 'Yellow'],
            ['name' => 'Cheese, Cheddar (1 oz)', 'calories' => 113, 'carbs' => 0.4, 'category' => 'Yellow'],
            ['name' => 'Milk, 2% (cup)', 'calories' => 122, 'carbs' => 12, 'category' => 'Yellow'],
            ['name' => 'Lean Beef (4 oz)', 'calories' => 200, 'carbs' => 0, 'category' => 'Yellow'],
            ['name' => 'Pork Tenderloin (4 oz)', 'calories' => 153, 'carbs' => 0, 'category' => 'Yellow'],
            ['name' => 'Granola (half cup)', 'calories' => 210, 'carbs' => 34, 'category' => 'Yellow'],
            ['name' => 'Popcorn, Air-popped (3 cups)', 'calories' => 93, 'carbs' => 19, 'category' => 'Yellow'],
            ['name' => 'Dark Chocolate (1 oz)', 'calories' => 170, 'carbs' => 13, 'category' => 'Yellow'],

            // RED - High carb / refined / processed
            ['name' => 'White Rice (cup)', 'calories' => 206, 'carbs' => 45, 'category' => 'Red'],
            ['name' => 'White Pasta (cup)', 'calories' => 220, 'carbs' => 43, 'category' => 'Red'],
            ['name' => 'White Bread (slice)', 'calories' => 79, 'carbs' => 15, 'category' => 'Red'],
            ['name' => 'Bagel (plain)', 'calories' => 277, 'carbs' => 53, 'category' => 'Red'],
            ['name' => 'Croissant', 'calories' => 231, 'carbs' => 26, 'category' => 'Red'],
            ['name' => 'Pancake (medium)', 'calories' => 175, 'carbs' => 22, 'category' => 'Red'],
            ['name' => 'Waffle (frozen)', 'calories' => 195, 'carbs' => 33, 'category' => 'Red'],
            ['name' => 'French Fries (medium)', 'calories' => 365, 'carbs' => 44, 'category' => 'Red'],
            ['name' => 'Potato Chips (1 oz)', 'calories' => 152, 'carbs' => 15, 'category' => 'Red'],
            ['name' => 'Tortilla Chips (1 oz)', 'calories' => 142, 'carbs' => 18, 'category' => 'Red'],
            ['name' => 'Pizza Slice (cheese)', 'calories' => 285, 'carbs' => 36, 'category' => 'Red'],
            ['name' => 'Hamburger Bun', 'calories' => 140, 'carbs' => 26, 'category' => 'Red'],
            ['name' => 'Hot Dog Bun', 'calories' => 120, 'carbs' => 22, 'category' => 'Red'],
            ['name' => 'Orange Juice (cup)', 'calories' => 112, 'carbs' => 26, 'category' => 'Red'],
            ['name' => 'Apple Juice (cup)', 'calories' => 114, 'carbs' => 28, 'category' => 'Red'],
            ['name' => 'Soda, Cola (12 oz)', 'calories' => 140, 'carbs' => 39, 'category' => 'Red'],
            ['name' => 'Sweet Tea (cup)', 'calories' => 90, 'carbs' => 23, 'category' => 'Red'],
            ['name' => 'Sports Drink (20 oz)', 'calories' => 140, 'carbs' => 34, 'category' => 'Red'],
            ['name' => 'Candy Bar (Snickers)', 'calories' => 250, 'carbs' => 33, 'category' => 'Red'],
            ['name' => 'Ice Cream (half cup)', 'calories' => 137, 'carbs' => 16, 'category' => 'Red'],
            ['name' => 'Donut (glazed)', 'calories' => 269, 'carbs' => 31, 'category' => 'Red'],
            ['name' => 'Muffin (blueberry)', 'calories' => 265, 'carbs' => 42, 'category' => 'Red'],
            ['name' => 'Cookie (chocolate chip)', 'calories' => 160, 'carbs' => 22, 'category' => 'Red'],
            ['name' => 'Cake Slice (frosted)', 'calories' => 352, 'carbs' => 52, 'category' => 'Red'],
            ['name' => 'Cereal, Frosted Flakes (cup)', 'calories' => 147, 'carbs' => 36, 'category' => 'Red'],
            ['name' => 'Cereal, Lucky Charms (cup)', 'calories' => 142, 'carbs' => 33, 'category' => 'Red'],
            ['name' => 'Granola Bar (sweet)', 'calories' => 190, 'carbs' => 29, 'category' => 'Red'],
            ['name' => 'Mac & Cheese (cup)', 'calories' => 310, 'carbs' => 36, 'category' => 'Red'],
            ['name' => 'Ramen Noodles (packet)', 'calories' => 380, 'carbs' => 52, 'category' => 'Red'],
            ['name' => 'Flour Tortilla (large)', 'calories' => 210, 'carbs' => 36, 'category' => 'Red'],
        ];
    }
}
