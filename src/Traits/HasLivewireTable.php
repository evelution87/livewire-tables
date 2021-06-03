<?php

namespace Evelution\LivewireTables\Traits;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait HasLivewireTable {
	
	public function livewireTableQuery( $query ) {
		return $query;
	}
	
	public function livewireSearchColumns() {
		return [ 'id', 'name' ];
	}
	
	public function livewireSearchAttributes() {
		return null;
	}
	
	public function livewireTableModel() {
		return $this->model ?? '\\App\\Models\\' . preg_replace( '/Controller$/', '', basename( get_called_class() ) );
	}
	
	protected function livewireTableParams( $merge = [] ) {
		return array_merge( [
			'model'        => $this->livewireTableModel(),
			'controller'   => get_called_class(),
			'initial_load' => false,
		],
			$merge );
	}
	
	protected function livewireTableModelSlug() {
		return Str::snake( Str::pluralStudly( class_basename( $this->livewireTableModel() ) ) );
	}
	
	protected function livewireTableActions( $item ) {
		
		$actions = [];
		
		if ( Route::has( $this->livewireTableModelSlug() . '.show' ) ) {
			$actions[ 'view' ] = [
				'route' => route( $this->livewireTableModelSlug() . '.show', $item->id ),
			];
		}
		if ( Route::has( $this->livewireTableModelSlug() . '.edit' ) ) {
			$actions[ 'edit' ] = [
				'route' => route( $this->livewireTableModelSlug() . '.edit', $item->id ),
			];
		}
		if ( Route::has( $this->livewireTableModelSlug() . '.destroy' ) ) {
			$actions[ 'destroy' ] = [
				'type'   => 'form',
				'method' => 'DELETE',
				'route'  => route( $this->livewireTableModelSlug() . '.destroy', $item->id ),
			];
		}
		
		return $actions;
	}
	
	public function livewireTableColumns() {
		return [
			'name'    => [
				'label'   => 'Name',
				'class'   => 'font-medium',
				'closure' => function ( $item ) {
					if ( Route::has( $this->livewireTableModelSlug() . '.show' ) ) {
						return '<a href="' . route( $this->livewireTableModelSlug() . '.show', $item->id ) . '">' . $item->name . '</a>';
					}
					
					return $item->name;
				},
			],
			'updated' => [
				'label'   => 'Updated',
				'type'    => 'timestamp',
				'class'   => 'w-0',
				'closure' => function ( $item ) {
					return $item->updated_at->format( 'j F Y, g:ia' );
				},
			],
			'actions' => [
				'label'   => 'Actions',
				'sr-only' => true,
				'type'    => 'actions',
				'class'   => 'w-0 text-right text-sm font-medium',
				'actions' => function ( $item ) {
					return $this->livewireTableActions( $item );
				},
			],
		];
	}
}