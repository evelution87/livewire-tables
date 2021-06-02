<?php

namespace Evelution\LivewireTables;

use Illuminate\Support\Facades\Cache;

class LivewireTables {
	
	public static function svg( $svg, $class = 'w-6 h-6' ) {
		
		$icon = [
			        'view'           => '<svg xmlns="http://www.w3.org/2000/svg" class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
			        'edit'           => '<svg xmlns="http://www.w3.org/2000/svg" class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
			        'destroy'        => '<svg xmlns="http://www.w3.org/2000/svg" class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
			        'cloud-download' => '<svg xmlns="http://www.w3.org/2000/svg" class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" /></svg>',
		        ][ $svg ] ?? null;
		
		if ( ! is_null( $icon ) ) {
			return $icon;
		}
		
		$icon = Cache::remember( 'svg.' . $svg, 3600, function() use ( $svg, $class ) {
			
			$dir = rtrim( preg_replace( '/src$/', '', __DIR__ ), '/\\' );
			if ( file_exists( $path = $dir . '/resources/svg/outline/' . $svg . '.svg' ) ) {
				return file_get_contents( $path );
			}
			
			return null;
			
		} );
		
		if ( ! is_null( $icon ) && ! empty( $icon ) ) {
			return str_replace( '<svg ', '<svg class="' . $class . '" ', $icon );
		}
		
		return $icon;
		
	}
}
