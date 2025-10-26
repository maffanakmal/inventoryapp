@extends('admin.layout.main')

@section('child-content')
    <div class="card shadow-sm mt-3">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 fw-semibold">Product Stock</h5>

            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                <form id="filterForm" class="d-flex align-items-center gap-2">
                    @csrf
                    <div class="form-group">
                        <select id="category_id" name="category_id" class="form-control select-category">
                            <option value="" selected>All Category</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <select id="stock" name="stock" class="form-select">
                            <option value="" selected>All Stock</option>
                            <option value="0-10">0 – 10</option>
                            <option value="11-50">11 – 50</option>
                            <option value="51-100">51 – 100</option>
                            <option value="101-500">101 – 500</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="stockTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>SKU</th>
                            <th>Variant Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th style="width:100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            stockTable();
            showSelect();
        });

        function stockTable() {
            let table = $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('master-data.stocks') }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        d.stock = $('#stock').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'variant_name',
                        name: 'variant_name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'variant_price',
                        name: 'variant_price'
                    },
                    {
                        data: 'stock_quantity',
                        name: 'stock_quantity'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#filterBtn, #category_id, #stock').on('click change', function() {
                table.ajax.reload();
            });
        }

        function selectCategory() {
            $('#category_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumInputLength: 0,
                dropdownParent: $('#category_id').parent(),
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
                url: '{{ route('master-data.stocks.create') }}',
                type: 'GET',
                success: function(response) {
                    if (response.status == 200) {

                        response.category.forEach(function(item) {
                            $('#category_id').append(
                                `<option value="${item.category_id}">${item.category_name}</option>`);
                        });

                        selectCategory();
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
    </script>
@endsection
