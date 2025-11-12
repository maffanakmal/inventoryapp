@extends('admin.layout.main')

@section('child-content')
    <div class="card shadow-sm mt-4 rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap rounded-top">
            <h5 class="mb-0 fw-semibold">Form Input Transaction</h5>
        </div>

        <div class="card-body">
            <form action="#" id="transactionForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="supplier" name="supplier"
                                placeholder="Input Supplier" value="{{ old('supplier') }}">
                            <label for="supplier">Supplier</label>
                            <div class="invalid-feedback" id="error-supplier"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="contact" name="contact"
                                placeholder="Input Contact" value="{{ old('contact') }}">
                            <label for="contact">Contact</label>
                            <div class="invalid-feedback" id="error-contact"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control" name="notes" id="notes" placeholder="Insert Notes" style="height: 100px" required>{{ old('notes') }}</textarea>
                            <label for="notes">Notes</label>
                            <div class="invalid-feedback" id="error-notes"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="variant_id" class="form-label fw-semibold">Variant</label>
                        <select id="variant_id" name="variant_id" class="form-select select-variant" required>
                            <option value="" selected disabled>Select Variant</option>
                        </select>
                        <div class="invalid-feedback" id="error-variant_id"></div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="batch_number" name="batch_number"
                                placeholder="Input Batch Number" value="{{ old('batch_number') }}">
                            <label for="batch_number">Batch Number</label>
                            <div class="invalid-feedback" id="error-batch_number"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                placeholder="Input Quantity" value="{{ old('quantity') }}">
                            <label for="quantity">Quantity</label>
                            <div class="invalid-feedback" id="error-quantity"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="price" name="price"
                                placeholder="Input Price" value="{{ old('price') }}">
                            <label for="price">Price</label>
                            <div class="invalid-feedback" id="error-price"></div>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="addToList" class="btn btn-success w-100 py-2">
                            <i class="fas fa-plus-circle me-1"></i> Add to List
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-4 rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap rounded-top">
            <h5 class="mb-0 fw-semibold">Stock List</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="stockTable" class="table table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Variant Name</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th style="width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                <h5 class="mb-0 fw-semibold">Grand Total:</h5>
                <h5 id="grandTotal" class="mb-0 text-success fw-bold">Rp 0</h5>
            </div>

            <div class="mt-4 text-end">
                <button type="button" id="submitTransaction" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-database me-1"></i> Submit
                </button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            showSelect();
        });

        function selectVariant() {
            $('#variant_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumInputLength: 0,
                dropdownParent: $('#variant_id').parent(),
                language: {
                    noResults: function() {
                        return "Results not found";
                    },
                    searching: function() {
                        return "Searching...";
                    }
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-search__field').focus();
                }, 0);
            });
        }

        function showSelect() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('transactions.create') }}',
                type: 'GET',
                success: function(response) {
                    if (response.status == 200) {

                        response.variants.forEach(function(item) {
                            $('#variant_id').append(
                                `<option value="${item.variant_id}">${item.sku} - ${item.variant_name}</option>`
                            );
                        });

                        selectVariant();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 500) {
                        let errorResponse = xhr.responseJSON;

                        Swal.fire({
                            icon: errorResponse.icon || "error",
                            title: errorResponse.title || "Error",
                            text: errorResponse.message ||
                                "An unexpected error occurred. Please try again later.",
                        });
                    }
                }
            });
        }

        let stockList = [];
        let grandTotal = 0;

        $('#addToList').on('click', function() {
            const supplier = $('#supplier').val();
            const contact = $('#contact').val();
            const variantId = $('#variant_id').val();
            const variantText = $('#variant_id option:selected').text();
            const batch = $('#batch_number').val();
            const qty = parseFloat($('#quantity').val());
            const price = parseFloat($('#price').val());

            if (!supplier) {
                Swal.fire('Warning', 'Please input supplier before adding items!', 'warning');
                return;
            }

            if (!variantId || !batch || !qty || !price) {
                Swal.fire('Warning', 'Please fill all fields before adding!', 'warning');
                return;
            }

            $('#supplier').prop('readonly', true);
            $('#contact').prop('readonly', true);

            const subtotal = qty * price;
            grandTotal += subtotal;

            const row = `
        <tr>
            <td>${$('#stockTable tbody tr').length + 1}</td>
            <td>${variantText}</td>
            <td>${batch}</td>
            <td>${qty}</td>
            <td>Rp ${price.toLocaleString()}</td>
            <td>Rp ${subtotal.toLocaleString()}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

            $('#stockTable tbody').append(row);
            $('#grandTotal').text(`Rp ${grandTotal.toLocaleString()}`);

            stockList.push({
                variant_id: variantId,
                batch_number: batch,
                quantity: qty,
                price: price
            });

            $('#batch_number, #quantity, #price').val('');
        });

        $(document).on('click', '.remove-item', function() {
            const row = $(this).closest('tr');
            const index = row.index();
            const subtotal = stockList[index].quantity * stockList[index].price;

            grandTotal -= subtotal;
            stockList.splice(index, 1);
            row.remove();

            $('#grandTotal').text(`Rp ${grandTotal.toLocaleString()}`);

            if (stockList.length === 0) {
                $('#supplier').prop('readonly', false);
                $('#contact').prop('readonly', false);
            }
        });


        $('#submitTransaction').on('click', function() {
            if (stockList.length === 0) {
                Swal.fire('Warning', 'No items to submit!', 'warning');
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('transactions.store') }}',
                type: 'POST',
                data: {
                    supplier: $('#supplier').val(),
                    contact: $('#contact').val(),
                    notes: $('#notes').val(),
                    items: stockList
                },
                success: function(response) {
                    Swal.fire('Success', 'Transaction submitted successfully!', 'success');
                    $('#stockTable tbody').empty();
                    $('#grandTotal').text('Rp 0');
                    stockList = [];
                    grandTotal = 0;
                    $('#transactionForm')[0].reset();
                    $('#variant_id').val(null).trigger('change');

                    $('#supplier').prop('readonly', false);
                    $('#contact').prop('readonly', false);
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to submit transaction.', 'error');
                }
            });
        });
    </script>
@endsection
