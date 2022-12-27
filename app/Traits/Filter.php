<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;

trait Filter
{
    /**
     * Get query filter.
     *
     * @param Request $request
     * @param Builder|EloquentBuilder $query
     * @param array $config
     * @return array
     */
    public function filter(Request $request, Builder|EloquentBuilder $query, $config = ['paginate' => true])
    {
        $_request = $request->except([
            'pagination', 'token', 'search', 'show_deleted', 'only_deleted',
        ]);

        /**
         * For search in spesific columns,
         * we use AND to filter data.
         */
        $query->where(function (Builder|EloquentBuilder $query) use ($_request) {
            foreach ($_request as $column => $value) {
                $query->where($column, 'LIKE', '%' . trim($value) . '%');
            }
        });

        /**
         * For search in any columns,
         * we use OR to filter data.
         */
        $columns = Schema::getColumnListing($query->from);
        // $columns = $query->columns;
        $search = $request->search ?? false;
        if ($search) {
            $query->orWhere(function (Builder|EloquentBuilder $query) use ($columns, $search) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'LIKE', '%' . trim($search) . '%');
                }
            });
        }

        /**
         * Handle tables with soft deletes
         */
        $onlyDeleted = $request->only_deleted ?? false;
        $showDeleted = $request->show_deleted ?? false;
        $hasDeletedAt = Schema::hasColumn($query->from, 'deleted_at');
        if ($hasDeletedAt && !$onlyDeleted) {
            if ($showDeleted) {
                $query->orWhereNotNull('deleted_at');
            }

            if (!$search) {
                $query->orWhereNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }
        }

        if ($hasDeletedAt && $onlyDeleted) {
            if (!$search) {
                $query->orWhereNotNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        $pagination = $request->pagination ?? 10;
        if ($config['paginate']) {
            return $query->simplePaginate($pagination);
        }

        return $query->get();
    }
}
