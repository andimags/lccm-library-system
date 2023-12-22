<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Http;


class GoogleBooksApiController extends Controller
{
    private function button($icon, $id, $className, $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-collection-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '><i class="' . $icon . '"></i></button>';
    }

    public function index()
    {
        $formats = \App\Models\Setting::where('field', 'format')->first()->holdingOptions()->get()->pluck('value')->toArray();
        $prefixes = \App\Models\Setting::where('field', 'prefix')->first()->holdingOptions()->pluck('value')->toArray();

        return view('google-books-api.index')->with([
            'formats' => $formats,
            'prefixes' => $prefixes
        ]);
    }

    public function search($keyword, $searchBy)
    {
        $books = null;

        try {
            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => $searchBy . ':' . $keyword,
                'maxResults' => 20,
                'key' => 'AIzaSyC0BYqTKxTbO_7QoqyO9vbJiBEfWWRp3Q0',
            ]);

            $books = $response->json()['items'];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return Datatables::of($books)
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';
                $html .= $this->button('fa-solid fa-plus', $row['id'], 'add');
                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('isbn', function ($row) {
                if (!isset($row['volumeInfo']['industryIdentifiers'])) {
                    return null;
                }

                $isbn = null;

                foreach ($row['volumeInfo']['industryIdentifiers'] as $identifier) {
                    if ($identifier['type'] === 'ISBN_13') {
                        $isbn = $identifier['identifier'];
                        break;
                    } elseif ($identifier['type'] === 'ISBN_10' && !$isbn) {
                        $isbn = $identifier['identifier'];
                    }
                }

                return $isbn;
            })
            ->addColumn('title', function ($row) {
                return $row['volumeInfo']['title'];
            })
            ->addColumn('author', function ($row) {
                if (isset($row['volumeInfo']['authors'])) {
                    $authors = array_slice($row['volumeInfo']['authors'], 0, 5) ?? '';
                    return implode(', ', $authors);
                }
            })
            ->rawColumns(['isbn', 'title', 'author', 'action'])
            ->make(true);
    }

    public function get($id)
    {
        try {
            $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$id}", [
                'key' => 'AIzaSyC0BYqTKxTbO_7QoqyO9vbJiBEfWWRp3Q0', // Replace with your actual Google Books API key
            ]);

            $book = $response->json()['volumeInfo'];

            $isbn = null;

            if (isset($book['industryIdentifiers'])) {
                foreach ($book['industryIdentifiers'] as $identifier) {
                    if ($identifier['type'] === 'ISBN_13') {
                        $isbn = $identifier['identifier'];
                        break;
                    } elseif ($identifier['type'] === 'ISBN_10' && !$isbn) {
                        $isbn = $identifier['identifier'];
                    }
                }
            }

            $subjects = isset($book['categories']) ? array_slice($book['categories'], 0, 5) : null;
            $uniqueCategories = [];

            if ($subjects) {
                foreach ($subjects as $subject) {
                    $categories = explode('/', $subject);
                    foreach ($categories as $category) {
                        if (!in_array($category, $uniqueCategories)) {
                            $uniqueCategories[] = $category;
                            if (count($uniqueCategories) == 5) {
                                break;
                            }
                        }
                    }
                    if (count($uniqueCategories) == 5) {
                        break;
                    }
                }
            }

            return response()->json(
                [
                    'title' => $book['title'],
                    'isbn' => $isbn,
                    'authors' => isset($book['authors']) ? array_slice(array_map([$this, 'convertToLastNameFirst'], $book['authors']), 0, 5) : null,
                    'publisher' => $book['publisher'] ?? '',
                    'subtitles' => $book['subtitle']  ?? '',
                    'physical_description' => 'No. of pages: ' . ($book['pageCount'] ?? 'Unknown'),
                    'subjects' => empty($uniqueCategories) ? null : $uniqueCategories,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function convertToLastNameFirst($fullName)
    {
        $names = explode(' ', $fullName);

        if (strpos($fullName, ',') !== false) {
            return $fullName;
        }

        $lastName = array_pop($names);

        return $lastName . ', ' . implode(' ', $names);
    }
}
