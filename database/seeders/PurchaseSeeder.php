<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Book;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        foreach ($users as $user) {
            $book = $books->random();

            Purchase::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'amount_paid' => $book->price,
                'purchased_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
