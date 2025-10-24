@extends('admin.layout.main')

@section('child-content')
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 fw-semibold">Products</h5>
            </div>
            <div class="btn-wrapper">
                <button onclick="productModal()" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Add Product
                </button>
                <button onclick="productSelectedDelete()" class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-plus me-1"></i> Delete Selected
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="productTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th style="width:150px">Action</th>
                            <th style="width:40px">
                                <input type="checkbox" id="checkAll" class="form-check-input" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="productModalLabel">Tambah data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="productForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_edit" id="product_edit">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="product_name" placeholder="Input Product Name"
                                name="product_name" value="{{ old('product_name') }}">
                            <label for="product_name" class="form-label">Product Name</label>
                            <div class="invalid-feedback" id="error-product_name"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select id="category_id" name="category_id" class="form-control select-category" required>
                                <option value="" selected disabled>Select Category</option>
                            </select>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="product_description" id="product_description"
                                placeholder="Insert Product Description" style="height: 100px" required>{{ old('product_description') }}</textarea>
                            <label for="product_description" class="form-label">Product Description</label>
                            <div class="invalid-feedback" id="error-product_description"></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveBtn" class="btn btn-primary btn-sm">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            productTable();
            selectCategory();
            showSelect();
        });

        function productTable() {
            var table = $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master-data.products') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'category.category_name',
                        name: 'category.category_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });
        }

        function productModal() {
            $('#productForm')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            method = 'create';
            product_name;

            $('#productModal').modal('show');
            $('#productModalLabel').text('Add Product');
            $('#saveBtn').text('Save');
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
                url: '{{ route('master-data.products.create') }}',
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

        $('#productForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#saveBtn');
            btn.prop('disabled', true).html(
                '<div class="spinner-border spinner-border-sm text-light mb-0" role="status"><span class="visually-hidden">Loading...</span></div>'
            );

            const formData = new FormData(this);
            let url = '{{ route('master-data.products.store') }}';
            let httpMethod = 'POST';

            if (method === 'update') {
                if (!product_id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid product ID.',
                    });
                    return;
                }

                url = '{{ route('master-data.products.update', '') }}/' + product_id;
                formData.append('_method', 'PUT');
                httpMethod = 'POST';
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: httpMethod,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    btn.prop('disabled', false).html(method === 'update' ? 'Edit' : 'Save');

                    if (response.status == 200) {
                        $('#productModal').modal('hide');
                        $('#productForm').trigger('reset');

                        $('#productTable').DataTable().ajax.reload();

                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(method === 'update' ? 'Edit' : 'Save');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            let inputField = $('[name="' + key + '"]');
                            let errorField = $('#error-' + key);
                            inputField.addClass('is-invalid');
                            if (errorField.length) {
                                errorField.text(value[0]);
                            }
                        });

                        $('input, select, textarea').on('input change', function() {
                            $(this).removeClass('is-invalid');
                            $('#error-' + $(this).attr('name')).text('');
                        });


                    } else if (xhr.status === 400) {
                        Swal.fire({
                            icon: xhr.responseJSON.icon,
                            title: xhr.responseJSON.title,
                            text: xhr.responseJSON.message
                        });
                        return;

                    } else {
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
        });

        function editProduct(e) {
            product_id = e.getAttribute('data-id');
            method = 'update';

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            let btn = $('#saveBtn');
            btn.prop('disabled', true).html(
                '<div class="spinner-border spinner-border-sm text-light mb-0" role="status"><span class="visually-hidden">Loading...</span></div>'
            );

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('master-data.products.show', '') }}/" + product_id,
                type: "GET",
                success: function(response) {
                    btn.prop('disabled', false).html(method === 'update' ? 'Edit' : 'Save');

                    $('#product_name').val(response.products.product_name);
                    $('#category_id').val(response.products.category_id);
                    $('#product_description').val(response.products.product_description);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(method === 'update' ? 'Edit' : 'Save');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            let inputField = $('[name="' + key + '"]');
                            let errorField = $('#error-' + key);
                            inputField.addClass('is-invalid');
                            if (errorField.length) {
                                errorField.text(value[0]);
                            }
                        });

                        $('input, select, textarea').on('input change', function() {
                            $(this).removeClass('is-invalid');
                            $('#error-' + $(this).attr('name')).text('');
                        });

                    } else {
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

            $('#productModal').modal('show');
            $('#productModalLabel').text('Edit Product');
            $('#saveBtn').text('Edit');
        }

        $('#checkAll').on('change', function() {
            $('.delete-checkbox').prop('checked', this.checked);
        });

        function productSelectedDelete() {
            const selectedIds = [];
            $('.delete-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select at least one product to delete.',
                });
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "Deleting selected product cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('master-data.products.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds
                        },
                        success: function(response) {
                            $('#productTable').DataTable().ajax.reload();
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#checkAll').prop('checked', false);
                                $('.delete-checkbox').prop('checked', false);
                                $('#productTable').DataTable().ajax.reload(null, false);
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An unexpected error occurred. Please try again later.",
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
