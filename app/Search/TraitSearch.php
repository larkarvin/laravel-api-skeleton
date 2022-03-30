<?php
namespace App\Search;
use Illuminate\Http\Request;

trait TraitSearch
{

    private static function determineSearch($query, $filters)
    {
        // Search for a user based on their name.
        if ($filters->has(self::searchParam)) {

            $search = '%' . trim($filters->input(self::searchParam)) . '%';
            $searchFields = self::searchFields;
            $query->where(function ($q) use ($searchFields, $search) {
                foreach($searchFields as $field){
                    $q->where($field, 'LIKE', $search);
                }
            });
        }

        return $query;
    }

    private static function determineSorting($query, $filters)
    {
        // SORTING
        $orderBy = 'DESC';
        if ($filters->has('order_by') ) {
            $orderBy = $filters->order_by;
        }
        
        if ($filters->has('sort_by') && in_array($filters->sort_by, self::sortables)) {
            $query->orderBy($filters->sort_by, $orderBy);
        }

        return $query;
    }

}