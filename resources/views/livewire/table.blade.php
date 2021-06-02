<div wire:init="loadItems">
	<div class="flex flex-col">
		
		<div class="-my-2 sm:-mx-6 lg:-mx-8">
			<div class="py-2 align-middle inline-block w-full sm:px-6 lg:px-8">
				
                {{ $items->links() }}
				
				<div class="shadow overflow-x-auto border-b border-gray-200 sm:rounded-lg my-3">
					
					@if($initial_load)
						<p class="text-xs font-bold uppercase tracking-widest text-center p-4 flex justify-center items-center bg-white text-gray-700">
							<svg class="animate-spin mr-3 h-5 w-5 text-light-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor"
								      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							Loading
						</p>
					@else
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
							<tr>
								
								@foreach ($columns as $column)
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
										@if($column['sr-only'] ?? false)
											<span class="sr-only">{{ $column['label'] }}</span>
										@else
											{{ $column['label'] }}
										@endif
									</th>
								@endforeach
							
							</tr>
							</thead>
							<tbody class="bg-white divide-y divide-gray-200 text-gray-900 text-sm">
							
							@foreach ($items as $item)
								<tr wire:key="item-{{ $item->id }}">
									
									@foreach ($columns as $column)
										<td class="px-6 py-4 whitespace-nowrap {{ $column['class'] ?? '' }}">
											
											@if(isset($column['closure']))
												
												{!! $column['closure']($item) ?? '' !!}
											
											@elseif(isset($column['attribute']) && ($column['boolean'] ?? false))
												
												<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->{$column['attribute']} ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
												{{ $item->{$column['attribute']} ? $column['true'] ?? 'true' : $column['false'] ?? 'false' }}
											</span>
											
											@elseif(isset($column['attribute']))
												
												{{ $item->{$column['attribute']} ?? '' }}
											
											@elseif('actions' === ($column['type'] ?? null))
												
												@foreach ($column['actions']($item) ?? [] as $action_key => $action)
													@if (isset($action['method']) && isset($action['route']))
														<form action="{{ $action['route'] }}" method="POST" class="inline-block">
															@csrf
															@isset($action['method']) @method($action['method']) @endisset
															<button class="text-light-blue-500 hover:text-red-500" title="Delete">
																{!! \Evelution\LivewireTables\LivewireTables::svg($action['svg'] ?? $action_key, $action['class'] ?? 'inline-block h-6 w-6') !!}
															</button>
														</form>
													@elseif(isset($action['route']))
														<a href="{{ $action['route'] }}" target="{{ $action['target'] ?? '_self' }}"
														   class="inline-block text-light-blue-500 hover:text-green-500">
															{!! \Evelution\LivewireTables\LivewireTables::svg($action['svg'] ?? $action_key, $action['class'] ?? 'inline-block h-6 w-6') !!}
														</a>
													@endif
												@endforeach
											
											@endif
										
										</td>
									@endforeach
								
								</tr>
							@endforeach
							
							</tbody>
						</table>
					
					@endif
				
				</div>

                {{ $items->links() }}
			
			</div>
		</div>
	
	</div>
</div>
