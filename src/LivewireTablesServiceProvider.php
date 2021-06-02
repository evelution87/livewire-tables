<?php

namespace Evelution\LivewireTables;

use Evelution\LivewireTables\Http\Livewire\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireTablesServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 */
	public function boot() {
		/*
		 * Optional methods to load your package assets
		 */
		// $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'livewire-tables');
		$this->loadViewsFrom( __DIR__ . '/../resources/views', 'livewire-tables' );
		// $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		// $this->loadRoutesFrom(__DIR__.'/routes.php');
		
		$this->registerLivewireComponents();
		$this->registerMacros();
		
		if ( $this->app->runningInConsole() ) {
			/*$this->publishes([
				__DIR__.'/../config/config.php' => config_path('livewire-tables.php'),
			], 'config');*/
			
			// Publishing the views.
			/*$this->publishes([
				__DIR__.'/../resources/views' => resource_path('views/vendor/livewire-tables'),
			], 'views');*/
			
			// Publishing assets.
			/*$this->publishes([
				__DIR__.'/../resources/assets' => public_path('vendor/livewire-tables'),
			], 'assets');*/
			
			// Publishing the translation files.
			/*$this->publishes([
				__DIR__.'/../resources/lang' => resource_path('lang/vendor/livewire-tables'),
			], 'lang');*/
			
			// Registering package commands.
			// $this->commands([]);
		}
	}
	
	protected function registerLivewireComponents() {
		
		Livewire::component( 'livewire-tables::table', Table::class );
		
	}
	
	protected function registerMacros() {
		
		Collection::macro( 'paginate', function( $perPage, $total = null, $page = null, $pageName = 'page' ) {
			$page = $page ?: LengthAwarePaginator::resolveCurrentPage( $pageName );
			
			return new LengthAwarePaginator(
				$this->forPage( $page, $perPage ),
				$total ?: $this->count(),
				$perPage,
				$page,
				[
					'path'     => LengthAwarePaginator::resolveCurrentPath(),
					'pageName' => $pageName,
				]
			);
		} );
		
		Arr::macro( 'after', function( $array, $key, $insert ) {
			
			$position = array_search( $key, array_keys( $array ), true ) + 1;
			$ret      = [];
			
			if ( $position >= count( $array ) ) {
				$ret = $array + $insert;
			} else {
				$i = 0;
				foreach ( $array as $key => $value ) {
					if ( $position == $i ++ ) {
						$ret += $insert;
					}
					
					$ret[ $key ] = $value;
				}
			}
			
			return $ret;
			
		} );
		
		Arr::macro( 'before', function( $array, $key, $insert ) {
			
			$position = array_search( $key, array_keys( $array ), true );
			$ret      = [];
			
			if ( $position >= count( $array ) ) {
				$ret = $array + $insert;
			} else {
				$i = 0;
				foreach ( $array as $key => $value ) {
					if ( $position == $i ++ ) {
						$ret += $insert;
					}
					
					$ret[ $key ] = $value;
				}
			}
			
			return $ret;
			
		} );
		
		Builder::macro( 'whereLike', function( $attributes, string $searchTerm ) {
			$this->where( function( Builder $query ) use ( $attributes, $searchTerm ) {
				foreach ( Arr::wrap( $attributes ) as $attribute ) {
					$query->when(
						str_contains( $attribute, '.' ),
						function( Builder $query ) use ( $attribute, $searchTerm ) {
							[ $relationName, $relationAttribute ] = explode( '.', $attribute );
							
							$query->orWhereHas( $relationName, function( Builder $query ) use ( $relationAttribute, $searchTerm ) {
								$query->where( $relationAttribute, 'LIKE', "%{$searchTerm}%" );
							} );
						},
						function( Builder $query ) use ( $attribute, $searchTerm ) {
							$query->orWhere( $attribute, 'LIKE', "%{$searchTerm}%" );
						}
					);
				}
			} );
			
			return $this;
		} );
		
	}
	
	/**
	 * Register the application services.
	 */
	public function register() {
		// Automatically apply the package configuration
		$this->mergeConfigFrom( __DIR__ . '/../config/config.php', 'livewire-tables' );
		
		// Register the main class to use with the facade
		$this->app->singleton( 'livewire-tables', function() {
			return new LivewireTables;
		} );
	}
}
