<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Products</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <style>
            .form-control:disabled, .form-control[readonly]{
                background-color: transparent;
                border: none;
            }
        </style>
    </head>
    <body>
        <div class="container mt-5">
            <h2>Add Product</h2>
            <form id="productForm">
                @csrf
                <input type="hidden" id="temp_datetime" name="temp_datetime">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="quantity_in_stock">Quantity in Stock</label>
                    <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" required>
                </div>
                <div class="form-group">
                    <label for="price_per_item">Price Per Item</label>
                    <input type="number" class="form-control" id="price_per_item" name="price_per_item" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            
            <h2 class="mt-5">Product List</h2>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity in Stock</th>
                        <th>Price Per Item</th>
                        <th>Datetime Submitted</th>
                        <th>Total Value Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTable">
                    @foreach ($data as $index => $product)
                        <tr id="{{ $index }}">
                            <td><input type="text" class="form-control product_name" value="{{ $product['product_name'] }}" disabled></td>
                            <td><input type="number" class="form-control quantity_in_stock" value="{{ $product['quantity_in_stock'] }}" disabled></td>
                            <td><input type="number" class="form-control price_per_item" value="{{ $product['price_per_item'] }}" disabled></td>
                            <td>{{ $product['datetime_submitted'] }}</td>
                            <td>{{ $product['total_value_number'] }}</td>
                            <td>
                                <button class="btn btn-success btn-edit" data-index="{{ $product['datetime_submitted'] }}" data-clickedRow="{{ $index }}">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total Sum</strong></td>
                        <td id="totalSum">{{ array_sum(array_column($data, 'total_value_number')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <script>
            $(document).ready(function(){
                let clickedRow = null;
                $('#productForm').on('submit', function(event){
                    event.preventDefault();

                    $.ajax({
                        url: "{{ route('product.submit') }}",
                        method: "POST",
                        data: $(this).serialize(),
                        success: function(response){
                            // Reset the form fields
                            $('#product_name').val('');
                            $('#quantity_in_stock').val('');
                            $('#price_per_item').val('');
                    

                            // Update the table
                            let newRow = `
                                <tr id="${clickedRow}">
                                    <td><input type="text" class="form-control product_name" value="${response.data.product_name}" disabled></td>
                                    <td><input type="number" class="form-control quantity_in_stock" value="${response.data.quantity_in_stock}" disabled></td>
                                    <td><input type="number" class="form-control price_per_item" value="${response.data.price_per_item}" disabled></td>
                                    <td>${response.data.datetime_submitted}</td>
                                    <td>${response.data.total_value_number}</td>
                                    <td>
                                        <button class="btn btn-success btn-edit" data-index="${response.data.datetime_submitted}">Edit</button>
                                    </td>
                                </tr>
                            `;

                            if ($("#temp_datetime").val()!=='') {
                                // After editing an existing row, replace the content
                                // console.log(response.data.datetime_submitted, "first", clickedRow);
                                $('#'+clickedRow).replaceWith(newRow);
                                // location.reload();
                            } else {
                                // If it's a new row, append it to the table
                                // console.log(response.data.datetime_submitted, "second");
                                $('#productTable').append(newRow);
                            }

                            // Update the total sum
                            $('#totalSum').text(response.total_sum);
                            $("#temp_datetime").val(undefined)
                            clickedRow = null;
                        },
                        error: function(error){
                            console.log(error.responseText);
                        }
                    });
                });

                $('#productTable').find('tr').click(function(){
                    clickedRow = $(this).index().toString();
                });

                // Edit
                $('#productTable').on('click', '.btn-edit', function(){
                    var index = $(this).data('index');
                    // clickedRow = $(this).index().toString();
                    var row = $(this).closest('tr');
                    $("#temp_datetime").val(index);

                    $('#product_name').val(row.find('.product_name').val());
                    $('#quantity_in_stock').val(row.find('.quantity_in_stock').val());
                    $('#price_per_item').val(row.find('.price_per_item').val());
                });
            });
        </script>
    </body>
</html>