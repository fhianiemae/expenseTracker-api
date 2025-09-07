<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Seed for the first user (or create one)
        $user = User::first() ?? User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        $defaults = [
            ['name'=>'Salary', 'type'=>'income'],
            ['name'=>'Freelance', 'type'=>'income'],
            ['name'=>'Food', 'type'=>'expense'],
            ['name'=>'Transport', 'type'=>'expense'],
            ['name'=>'Bills', 'type'=>'expense'],
        ];

        foreach ($defaults as $cat) {
            Category::firstOrCreate([
                'user_id' => $user->id,
                'name'    => $cat['name'],
                'type'    => $cat['type'],
            ]);
        }
    }
}
