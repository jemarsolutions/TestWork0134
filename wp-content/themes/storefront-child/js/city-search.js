jQuery(document).ready(function($) {
    $('#city-search').on('input', function() {
        var query = $(this).val(); // Get search input
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'city_search',
                query: query
            },
            success: function(response) {
                var tableBody = $('#city-table tbody');
                tableBody.empty(); // Clear existing table rows
                
                if (response && response.length > 0) {
                    $.each(response, function(index, city) {
                        var row = '<tr>' +
                            '<td>' + (city.country_name || 'N/A') + '</td>' +
                            '<td>' + (city.city_name || 'N/A') + '</td>' +
                            '<td>' + (city.temperature || 'N/A') + '</td>' +
                            '</tr>';
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append('<tr><td colspan="3">No data found</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Request Error:', error);
            }
        });
    });
});