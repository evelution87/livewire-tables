<?php

namespace Evelution\LivewireTables\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
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
    
    public array $filter = [];
    
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
    
    public function filterQuery( $query ) {
        $controller = ( new $this->controller );
        
        if ( !empty( $this->search ) ) {
            $query = $query->whereLike( $controller->livewireSearchColumns(), $this->search );
        }
        
        foreach ( $this->filter as $key => $value ) {
            if ( $this->is_model_attribute( $key ) ) {
                $query = $query->where( $key, $value );
            }
        }
        
        return $query;
    }
    
    public function filterAttributes( $item ) {
        $controller = ( new $this->controller );
        
        foreach ( $this->filter as $attribute => $value ) {
            if ( !$this->is_model_attribute( $attribute ) ) {
                if ( $item->{$attribute} != $value ) {
                    return false;
                }
            }
        }
        
        foreach ( $controller->livewireSearchAttributes() as $attribute ) {
            if ( str_contains( strtolower( $item->{$attribute} ), strtolower( $this->search ) ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    private function is_model_attribute( $column ) : bool {
        
        $model = new $this->model;
        
        if ( $model->hasGetMutator( $column ) ) {
            return false;
        }
        
        return Schema::hasColumn( $model->getTable(), $column );
        
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
                $model = $model->get()->filter( [ $this, 'filterAttributes' ] );
            } else {
                $model = $this->filterQuery( $model );
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
