<?php

namespace Evelution\LivewireTables\Http\Livewire;

use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component {

    use WithPagination;

    /**
     * TODO:
     * -    Add support for filters
     *      pre-query closure and post-query closure
     * -    Add support for searching
     *      pre-query and post-query
     * -    Make $per_page adjustable
     * -    Add support for livewire-targeting actions (e.g. instant delete, confirmation)
     * -    Improve pagination template
     * -    Add filters template
     */

    public string $model;
    public string $controller;
    public int $per_page = 10;
    public bool $initial_load = true;

    public string $search = '';

    public function loadItems() {
        $this->initial_load = false;
    }

    public function paginationView() {
        return 'livewire-tables::pagination';
    }

    public function updatedSearch( $value ) {
        $this->resetPage();
    }

    public function updatedPerPage( $value ) {
        $this->resetPage();
    }

    public function controllerMethod( $method, $params ) {
        $result = ( new $this->controller )->{$method}( ...( Arr::wrap( $params ) ) );

    }

    public function searchQuery( $query ) {
        $controller = ( new $this->controller );

        if ( empty( $this->search ) ) {
            return $query;
        }

        return $query->whereLike( $controller->livewireSearchColumns(), $this->search );
    }

    public function searchAttributes( $item ) {
        $controller = ( new $this->controller );

        debug( $controller->livewireSearchAttributes() );

        foreach ( $controller->livewireSearchAttributes() as $column ) {
            debug( $item->{$column} );
            if ( str_contains( strtolower( $item->{$column} ), strtolower( $this->search ) ) ) {
                return true;
            }
        }

        return false;
    }

    private function is_model_attribute( $column ) : bool {

        if ( $this->model->hasGetMutator( $column ) ) {
            return false;
        }

        return Schema::hasColumn( $this->model->getTable(), $column );

    }

    public function render_data() {

        $data = [];

        $controller = ( new $this->controller );

        // Get the column settings from the controller
        $data[ 'columns' ] = $controller->livewireTableColumns();

        // Only load items loadItems() has been called by wire:init
        if ( !$this->initial_load ) {
            // Get base query and apply pre-query adjustments
            $model = $controller->livewireTableQuery( new $this->model );

            // TODO If post-query filters or searching are needed use $model = $model->get();
            // Avoid post-query filters unless absolutely necessary

            if ( !is_null( $controller->livewireSearchAttributes() ) ) {
                $model = $model->get()->filter( [ $this, 'searchAttributes' ] );
            } else {
                $model = $this->searchQuery( $model );
            }

        }

        // Paginate the result of the query, or the result collection if already queried
        // This is always applied (even to the empty default collection), so the pagination functions will always work
        $data[ 'items' ] = ( $model ?? collect( [] ) )->paginate( $this->per_page );

        return $data;

    }

    public function render() {

        return view( 'livewire-tables::livewire.table', $this->render_data() );
    }
}
