<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;

trait Filter
{
    /**
     * Get query filter.
     *
     * @param Request $request
     * @param Builder $query
     * @param array $config
     * @return array
     */
    public function filter(Request $request, Builder $query, $config = ['paginate' => true])
    {
        $_request = $request->except(['pagination', 'token', 'search', 'show_deleted']);

        /**
         * For search in spesific columns,
         * we use AND to filter data.
         */
        $query->where(function (Builder $query) use ($_request) {
            foreach ($_request as $column => $value) {
                $query->where($column, 'LIKE', '%' . trim($value) . '%');
            }
        });

        /**
         * For search in any columns,
         * we use OR to filter data.
         */
        $columns = $query->columns;
        $search = $request->search ?? false;
        if ($search) {
            $query->orWhere(function (Builder $query) use ($columns, $search) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'LIKE', '%' . trim($search) . '%');
                }
            });
        }

        /**
         * Handle tables with soft deletes
         */
        $showDeleted = $request->show_deleted ?? false;
        $hasDeletedAt = Schema::hasColumn($query->from, 'deleted_at');
        if ($hasDeletedAt) {
            $where = 'whereNull';
            if ($showDeleted) $where = 'orWhereNotNull';
            $query->$where('delete');
        }

        $pagination = $request->pagination ?? 10;
        if ($config['paginate']) {
            return $query->simplePaginate($pagination);
        }

        return $query->get();
    }
}
