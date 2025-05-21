<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // Lister les achats
    public function index()
    {
        $user = Auth::user();

        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        if ($user->role === 'admin') {
            $purchases = Purchase::with(['book', 'user'])->get();
        } else {
            $purchases = Purchase::with('book')
                ->where('user_id', $user->id)
                ->get();
        }

        return response()->json(['data' => $purchases]);
    }

    // Acheter un livre
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'amount_paid' => 'nullable|numeric'
        ]);

        $user = Auth::user();

        $book = Book::findOrFail($request->book_id);

        if ($book->quantity < 1) {
            return response()->json(['error' => 'Livre non disponible'], 400);
        }

        // Diminuer le stock
        $book->decrement('quantity');

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount_paid' => $request->amount_paid ?? $book->price,
            'purchased_at' => now()
        ]);

        return response()->json(['message' => 'Livre acheté avec succès', 'purchase' => $purchase], 201);
    }

    // Voir un achat
    public function show($id)
    {
        $purchase = Purchase::with('book')->findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $purchase->user_id !== $user->id) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        return response()->json($purchase);
    }

    // Supprimer un achat (admin uniquement)
    public function destroy($id)
    {
        $user = Auth::user();
        $purchase = Purchase::findOrFail($id);

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $purchase->delete();
        return response()->json(['message' => 'Achat supprimé'], 200);
    }
}
