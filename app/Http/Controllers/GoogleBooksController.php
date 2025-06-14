<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBooksController extends Controller
{
    /**
     * Mencari buku di Google Books API berdasarkan ISBN.
     */
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function __construct()
    {
        $this->apiKey = config('services.google.books_api_key');
    }

    // Untuk fitur Barcode Scanner
    public function findByIsbn($isbn)
    {
        return $this->searchBooks('isbn:' . $isbn, true);
    }

    // Untuk fitur Search Bar
    public function search(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['message' => 'Search query "q" is required.'], 400);
        }
        return $this->searchBooks($query);
    }

    // Helper utama untuk berkomunikasi dengan Google API
    private function searchBooks($query, $takeFirst = false)
    {
        if (empty($this->apiKey)) {
            Log::error('Google Books API Key is not configured.');
            return response()->json(['message' => 'Server configuration error.'], 500);
        }

        try {
            $response = Http::get($this->baseUrl, [
                'q' => $query,
                'key' => $this->apiKey,
                'maxResults' => $takeFirst ? 1 : 20
            ]);

            if ($response->failed()) {
                return response()->json(['message' => 'Failed to connect to Google Books API.'], 502);
            }

            $data = $response->json();
            if (empty($data['items'])) {
                return $takeFirst ? response()->json(['message' => 'Book not found.'], 404) : response()->json([]);
            }

            $formattedBooks = array_map([$this, 'formatGoogleBookData'], $data['items']);

            return $takeFirst ? response()->json($formattedBooks[0]) : response()->json($formattedBooks);

        } catch (\Exception $e) {
            Log::error('GoogleBooksController Exception: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Helper untuk mengubah response JSON dari Google menjadi format yang kita inginkan.
     */
    private function formatGoogleBookData($item)
    {
        $info = $item['volumeInfo'] ?? [];

        $isbn13 = null;
        $isbn10 = null;
        if (!empty($info['industryIdentifiers'])) {
            foreach ($info['industryIdentifiers'] as $identifier) {
                if ($identifier['type'] === 'ISBN_13') $isbn13 = $identifier['identifier'];
                if ($identifier['type'] === 'ISBN_10') $isbn10 = $identifier['identifier'];
            }
        }

        return [
            'title' => $info['title'] ?? 'No Title Available',
            'author' => implode(', ', $info['authors'] ?? ['Unknown Author']),
            'publisher' => $info['publisher'] ?? null,
            'year' => isset($info['publishedDate']) ? (int)substr($info['publishedDate'], 0, 4) : null,
            'pages' => $info['pageCount'] ?? 0,
            'description' => $info['description'] ?? null,
            'isbn' => $isbn13 ?? $isbn10,
            'cover_photo_path' => isset($info['imageLinks']['thumbnail']) ? str_replace('http://', 'https://', $info['imageLinks']['thumbnail']) : null,
            'genre' => implode(', ', $info['categories'] ?? []),
        ];
    }
}
