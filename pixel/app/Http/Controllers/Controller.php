<?php namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    /**
     * @var int Count of per page records for use in pagination
     */
    public $perPage = 10;

    /**
     * @var int Count of max per page records for use in pagination
     */
    protected $maxPerPage = 100;

    /**
     * @var int Number of page for use in pagination
     */
    public $page = 1;

    /**
     * Controller constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        # Set perPage from request
        $requestPerPage = $request->get('perPage', $this->perPage);
        $this->perPage  = $requestPerPage > $this->maxPerPage ? $this->maxPerPage : $requestPerPage;

        # Set number of page from request
        $this->page = $request->get('page', $this->page);
    }

    /**
     * @param Builder $query
     *
     * @return JsonResponse
     */
    public function respondPaginate(Builder $query): JsonResponse
    {
        $paginate = $query->paginate($this->perPage, ['*'], null, $this->page);

        return response()->json([
            'data'      => $paginate->items(),
            'last_page' => $paginate->lastPage(),
            'total'     => $paginate->total(),
        ]);
    }
}
