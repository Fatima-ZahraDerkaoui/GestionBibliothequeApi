<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BookPreview;

class BookPreviewController extends Controller
{
    public function index()
    {
        return response()->json(
            BookPreview::with('book')->where('user_id', Auth::id())->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $preview = BookPreview::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
        ]);

        return response()->json($preview, 201);
    }
}
