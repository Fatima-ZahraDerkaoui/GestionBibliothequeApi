<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;

class BorrowingController extends Controller
{
    // Affiche les emprunts de l'utilisateur connecté
    public function index()
    {
        $user = Auth::user();

        // Si admin, afficher tous les emprunts
        if ($user->role === 'admin') {
            $borrowings = Borrowing::with(['book', 'user'])->get();
        } else {
            $borrowings = Borrowing::with('book')
                ->where('user_id', $user->id)
                ->get();
        }

        return response()->json($borrowings);
    }

    // Emprunter un livre
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Les administrateurs ne peuvent pas emprunter de livres.'], 403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->quantity < 1) {
            return response()->json(['message' => 'Livre indisponible'], 400);
        }

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $book->decrement('quantity');

        return response()->json([
            'message' => 'Livre emprunté avec succès',
            'borrowing' => $borrowing,
        ], 201);
    }

    // Retourner un livre
    public function returnBook($id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Les administrateurs ne retournent pas de livres.'], 403);
        }

        $borrowing = Borrowing::where('user_id', $user->id)->findOrFail($id);

        if ($borrowing->returned_at) {
            return response()->json(['message' => 'Ce livre a déjà été retourné.'], 400);
        }

        $borrowing->update([
            'returned_at' => now(),
        ]);

        $borrowing->book->increment('quantity');

        return response()->json(['message' => 'Livre retourné avec succès']);
    }
}
