<?php
namespace App\Search;
use Illuminate\Http\Request;

use App\Models\Task;
use Carbon\Carbon;
use App\Search\TraitSearch;

class TaskSearch 
{
    use TraitSearch;

    const searchParam = 'search';
    const searchFields = ['title','description'];
    const sortables = ['title','description','due_date','created_at','completed_at','priority'];

    public static function apply(Request $filters)
    {

        $query = Task::with(['tags','media'])->where('user_id', auth()->id());


        $query = self::determineSearch($query, $filters);

        // completed date filter
        if ($filters->has('completed_date_from') && $filters->has('completed_date_to')) {
            $from = new Carbon($filters->completed_date_from);
            $to   = new Carbon($filters->completed_date_to);

            $query->whereBetween('completed_at', [$from, $to]);
        }
        // completed date filter
        if ($filters->has('archived_date_from') && $filters->has('archived_date_to')) {
            $from = new Carbon($filters->archived_date_from);
            $to   = new Carbon($filters->archived_date_to);

            $query->whereBetween('archived_at', [$from, $to]);
            $query->withArchived();
        }
        // created date from
        if ($filters->has('created_date_from') && $filters->has('created_date_to')) {
            $from = new Carbon($filters->created_date_from);
            $to   = new Carbon($filters->created_date_to);

            $query->whereBetween('created_at', [$from, $to]);
        }
        // due date from
        if ($filters->has('due_date_from') && $filters->has('due_date_to')) {
            $from = new Carbon($filters->due_date_from);
            $to   = new Carbon($filters->due_date_to);

            $query->whereBetween('due_date', [$from, $to]);
        }

        // priority filter
        if ($filters->has('priority')) {
            $query->where('priority', $filters->priority);
        }

        $query = self::determineSorting($query, $filters);

        return $query;
    }
}