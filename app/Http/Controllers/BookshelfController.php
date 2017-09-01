<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Book;

class BookshelfController extends Controller {

    const DEFAULT_BOOKS_LIMIT = 10;
    const DEFAULT_BOOKS_OFFSET = 0;

    /**
     *  Validation rules for book fields
     *
     * @var array
     */
    protected $books_validate_rules = [
        'title' => 'required|max:150',
        'author' => 'required|max:100',
        'publication_year' => 'nullable|digits_between:3,4',
        'description' => 'required|max:2000'
    ];

    /**
     *  Validation rules for book cover image
     *
     * @var array
     */
    protected $book_cover_validate_rules = [
        'cover' => 'required|file|image|max:500'
    ];

    /**
     *  Validation messages for book fields
     *
     * @var array
     */
    protected $books_validate_messages = [
        'title.required' => 'Поле с названием книги обязатлеьно для заполненния',
        'title.max' => 'Введено слишком длинное название для книги',
        'author.required' => 'Поле автора обязатлеьно для заполненния',
        'author.max' => 'Введено слишком длинное имя автора книги книги',
        'publication_year.digits_between' => 'Указан недопустимый год издания книги',
        'description.required' => 'Поле описания обязатльено для заполнения',
        'description.max' => 'Введено слишком длинное описание для книги'
    ];

    /**
     *  Validation rules for book cover image
     *
     * @var array
     */
    protected $book_cover_validate_messages = [
        'cover.required' => 'Не задан фай для обложки книги',
        'cover.image' => 'Обложка для книги должна быть изображением',
        'cover.mzx' => 'Размер файла не должен превышать 500 кБ'
    ];

    /**
     * Valid sortable fields for request
     *
     * @var array
     */
    protected $sorting_books_fields = [
        'title', 'author'
    ];

    /**
     * Get list of existed books
     *
     * @param Request $request
     * @return string json
     */
    public function getBooks(Request $request)
    {
        $books_model = new Book;
        $books_table = DB::table($books_model->getTable());

        $search_query = $request->input('q');
        $books_table = $this->filterBooksTable($books_table, $search_query);

        $sort_fields = $this->getSortingFieldsFromRequest($request);
        foreach ($sort_fields as $field_name => $order) {
            $books_table->orderBy($field_name, $order);
        }

        $limit = (int)$request->input('limit', self::DEFAULT_BOOKS_LIMIT);
        $limit = $limit > 0 ? $limit : self::DEFAULT_BOOKS_LIMIT;
        $offset = (int)$request->input('offset', self::DEFAULT_BOOKS_OFFSET);

        $books = $books_table
                    ->whereNull('deleted_at')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
        $books_count = $this->getBooksCount($search_query);

        $pages = ceil($books_count/$limit);
        $current_page = $offset < $books_count ? ceil(($offset + 1) / $limit) : -1;

        $response = [
            'books' => $books,
            'total_count' => $books_count,
            'pages' => $pages,
            'page' => $current_page
        ];

        return response()->json($response)->header('Content-type', 'application/json');
    }

    /**
     *  Method applying search filter for query if it set
     *
     * @param Builder $books_table
     * @param string $filter
     * @return Builder|static
     */
    protected function filterBooksTable(Builder $books_table, $filter = '')
    {
        if (!empty($filter)) {
            $books_table = $books_table->whereRaw('LOWER(title) like ?', ['%'.strtolower($filter).'%'])
                ->orWhereRaw('LOWER(author) like ?', ['%'.strtolower($filter).'%']);
        }
        return $books_table;
    }

    /**
     * Method returns dictionary of fields as key and theirs sorting order as value
     *
     * @param Request $request
     * @return array
     */
    protected function getSortingFieldsFromRequest(Request $request)
    {
        $order_fields = [];
        $order_string = $request->input('order');
        if (!empty($order_string)) {
            $fields = explode(',', $order_string);
            foreach ($fields as $field) {
                $this->validateSortingField($field);
                $order_fields[ltrim($field, '-')] = $this->getFieldOrder($field);
            }
        }
        return $order_fields;
    }

    /**
     *  Method validate field name for requested sorting
     *
     * @param string $field_name
     */
    protected function validateSortingField($field_name)
    {
        if (!in_array(ltrim($field_name, '-'), $this->sorting_books_fields)) {
            abort(400, 'Invalid field name for sotring');
        }
    }

    /**
     * Getting sorting order - ascending or descending - from given field name
     * If field name starts with minus - descending sort
     *
     * @param string $field_string
     * @return string
     */
    protected function getFieldOrder($field_string = '')
    {
        $order = 'asc';
        if (strpos($field_string, '-') === 0) {
            $order = 'desc';
        }
        return $order;
    }

    /**
     * Get total quantity of books
     *
     * @param string $filter
     * @return integer
     */
    protected function getBooksCount($filter = '')
    {
        $books_model = new Book;
        $books_table = DB::table($books_model->getTable());
        $books_table = $this->filterBooksTable($books_table, $filter);
        $books_quantity = $books_table->count();

        return $books_quantity;
    }

    /**
     * Get some book by its identifier
     *
     * @param integer $id
     * @return string json
     */
    public function getBook($id)
    {
        $book = Book::findOrFail($id);

        return response()->json($book)->header('Content-type', 'application/json');
    }

    /**
     * Get cover of book by books identifier
     *
     * @param integer $book_id
     * @return object image
     */
    public function getBookCover($book_id)
    {
        $book = Book::findOrFail($book_id);

        if (!empty($book->cover)) {
            $covers_path = public_path().'/covers/';
            $book_cover_file = $covers_path.$book->cover;
            $content = @file_get_contents($book_cover_file);
            if ($content === false) {
                $book_cover_file = $covers_path.'default-cover.jpg';
                $content = @file_get_contents($covers_path.'default-cover.jpg');
            }
            $type = mime_content_type($book_cover_file);
            $status_code = 200;
            $headers = ['Content-type' => $type];
        } else {
            $content = '';
            $status_code  = 204;
            $headers = ['Content-type' => 'application/json'];
        }

        return response()->make($content, $status_code)->withHeaders($headers);
    }

    /**
     * Adding new book at your bookshelf
     *
     * @param Request $request
     * @return string json
     */
    public function addBook(Request $request)
    {
        $this->validate($request, $this->books_validate_rules, $this->books_validate_messages);

        $book = new Book;

        $book->fill($request->all());

        try {
            $book->save();
            $status_code = 201;
            $response = ['success' => 'created', 'book_id' => $book->id];
        } catch (\Exception $e) {
            $status_code = 500;
            $response = ['error' => $e->getMessage()];
        } finally {
            return response()->json($response, $status_code)->header('Content-type', 'application/json');
        }
    }

    /**
     * Adding cover to book by given book identifier
     *
     * @param integer $book_id
     * @param Request $request
     * @return string json
     */
    public function addBookCover(Request $request, $book_id)
    {
        $book = Book::findOrFail($book_id);

        $this->validate($request, $this->book_cover_validate_rules, $this->book_cover_validate_messages);

        $cover = $request->file('cover');
        if ($cover->isValid()) {
            $cover_file_name = 'book-'.$book->id.'-'.$cover->getClientOriginalName();
            $cover->move( public_path().'/covers/', $cover_file_name);
            $book->cover = $cover_file_name;
            $book->save();
            $status_code = 201;
            $response = ['success' => 'created'];
        } else {
            $status_code = 500;
            $response = ['error' => $cover->getErrorMessage()];
        }
        return response()->json($response, $status_code)->header('Content-type', 'application/json');
    }

    /**
     * Updating book data in database by its identifier
     *
     * @param integer $id
     * @param Request $request
     * @return string json
     */
    public function updateBook($id, Request $request)
    {
        $this->validate($request, $this->books_validate_rules, $this->books_validate_messages);

        $book = Book::findOrFail($id);

        $book->fill($request->all());

        try {
            $book->save();
            $status_code = 200;
            $response = ['success' => 'updated', 'book_id' => $book->id];
        } catch (\Exception $e) {
            $status_code = 500;
            $response = ['error' => $e->getMessage()];
        } finally {
            return response()->json($response, $status_code)->header('Content-type', 'application/json');
        }
    }

    /**
     * Soft deleting book in database by its identifier
     *
     * @param integer $id
     * @return string json
     */
    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        try {
            $book->delete();
            $response = ['success' => 'deleted'];
            $status_code = 200;
        } catch (\Exception $e) {
            $status_code = 500;
            $response = ['error' => $e->getMessage()];
        } finally {
            return response()->json($response, $status_code)->header('Content-type', 'application/json');
        }
    }
}