<div class="form-group mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
        id="{{ $name }}" 
        class="form-control select2-route"
        data-placeholder="{{ $placeholder }}"
        {{ $multiple ? 'multiple' : '' }}
        {{ $required ? 'required' : '' }}
        style="width: 100%;"
    >
        @if($selectedRoutes && count($selectedRoutes) > 0)
            @foreach($selectedRoutes as $route)
                <option value="{{ $route['id'] }}" data-price="{{ $route['price'] }}" selected>
                    {{ $route['name'] }} ({{ $route['start_point'] }} - {{ $route['end_point'] }}) - {{ number_format($route['external_cost'], 2) }} {{ config('settings.currency_symbol', 'SAR') }}
                </option>
            @endforeach
        @endif
    </select>
    
    <div id="route-total" class="mt-2 text-success font-weight-bold d-none">
        <span>إجمالي تكلفة خطوط السير: </span>
        <span id="route-total-amount">0.00</span>
        <span>{{ config('settings.currency_symbol', 'SAR') }}</span>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for route selection
    $('.select2-route').select2({
        ajax: {
            url: '{{ route("routes.select") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: data.pagination
                };
            },
            cache: true
        },
        placeholder: '{{ $placeholder }}',
        minimumInputLength: 0,
        allowClear: true,
        templateResult: formatRoute,
        templateSelection: formatRouteSelection,
        escapeMarkup: function (markup) { return markup; },
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            },
            inputTooShort: function(args) {
                return "الرجاء إدخال " + args.minimum + " حرف أو أكثر";
            }
        }
    });

    // Format how the route is displayed in the dropdown
    function formatRoute(route) {
        if (route.loading) return route.text;
        
        var $container = $(
            '<div class="d-flex justify-content-between align-items-center">' +
                '<span>' + route.text + '</span>' +
                '<span class="badge bg-primary">' + route.formatted_price + '</span>' +
            '</div>'
        );
        
        return $container;
    }
    
    // Format how the selected route is displayed
    function formatRouteSelection(route) {
        return route.text || route.name || route.id;
    }
    
    // Calculate and update the total cost of selected routes
    function updateRouteTotal() {
        var total = 0;
        var $select = $('.select2-route');
        var $routeTotal = $('#route-total');
        var $routeTotalAmount = $('#route-total-amount');
        
        // Get all selected options
        $select.find('option:selected').each(function() {
            var price = parseFloat($(this).data('price')) || 0;
            total += price;
        });
        
        // Update the total display
        if (total > 0) {
            $routeTotalAmount.text(total.toFixed(2));
            $routeTotal.removeClass('d-none');
            
            // Trigger an event to update the rental total
            $(document).trigger('routeTotalUpdated', [total]);
        } else {
            $routeTotal.addClass('d-none');
            $(document).trigger('routeTotalUpdated', [0]);
        }
    }
    
    // Update total when routes change
    $('.select2-route').on('change', updateRouteTotal);
    
    // Initial calculation
    updateRouteTotal();
});
</script>
@endpush