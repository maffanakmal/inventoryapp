@extends('admin.layout.main')

@section('child-content')
    <div class="card shadow-sm mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 fw-semibold">Categories</h5>
            </div>
            <div class="btn-wrapper">
                <button onclick="categoryModal()" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Add Category
                </button>
                <button onclick="categorySelectedDelete()" class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-trash me-1"></i> Delete Selected
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="categoryTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Category Name</th>
                            <th style="width:100px">Action</th>
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

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="categoryModalLabel">Tambah data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="categoryForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category_edit" id="category_edit">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="category_name" placeholder="Input Category Name"
                                name="category_name" value="{{ old('category_name') }}">
                            <label for="category_name" class="form-label">Category Name</label>
                            <div class="invalid-feedback" id="error-category_name"></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </button>
                    <button type="submit" id="saveBtn" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-database me-1"></i> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            categoryTable();
        });

        function categoryTable() {
            var table = $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master-data.categories') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
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

        function categoryModal() {
            $('#categoryForm')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            method = 'create';
            category_name;

            $('#categoryModal').modal('show');
            $('#categoryModalLabel').text('Add Category');
            $('#saveBtn').text('Save');
        }

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#saveBtn');
            btn.prop('disabled', true).html(
                '<div class="spinner-border spinner-border-sm text-light mb-0" role="status"><span class="visually-hidden">Loading...</span></div>'
            );

            const formData = new FormData(this);
            let url = '{{ route('master-data.categories.store') }}';
            let httpMethod = 'POST';

            if (method === 'update') {
                if (!category_id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid category ID.',
                    });
                    return;
                }

                url = '{{ route('master-data.categories.update', '') }}/' + category_id;
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
                    btn.prop('disabled', false).html(method === 'update' ? '<i class="fa-solid fa-database me-1"></i> Edit' : '<i class="fa-solid fa-database me-1"></i> Save');

                    if (response.status == 200) {
                        $('#categoryModal').modal('hide');
                        $('#categoryForm').trigger('reset');

                        $('#categoryTable').DataTable().ajax.reload();

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
                    btn.prop('disabled', false).html(method === 'update' ? '<i class="fa-solid fa-database me-1"></i> Edit' : '<i class="fa-solid fa-database me-1"></i> Save');

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

        function editCategory(e) {
            category_id = e.getAttribute('data-id');
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
                url: "{{ route('master-data.categories.show', '') }}/" + category_id,
                type: "GET",
                success: function(response) {
                    btn.prop('disabled', false).html(method === 'update' ? '<i class="fa-solid fa-database me-1"></i> Edit' : '<i class="fa-solid fa-database me-1"></i> Save');

                    $('#category_name').val(response.categories.category_name);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(method === 'update' ? '<i class="fa-solid fa-database me-1"></i> Edit' : '<i class="fa-solid fa-database me-1"></i> Save');

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

            $('#categoryModal').modal('show');
            $('#categoryModalLabel').text('Edit Category');
            $('#saveBtn').text('Edit');
        }

        $('#checkAll').on('change', function() {
            $('.delete-checkbox').prop('checked', this.checked);
        });

        function categorySelectedDelete() {
            const selectedIds = [];
            $('.delete-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select at least one category to delete.',
                });
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "Deleting selected categories cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('master-data.categories.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds
                        },
                        success: function(response) {
                            $('#categoryTable').DataTable().ajax.reload();
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#checkAll').prop('checked', false);
                                $('.delete-checkbox').prop('checked', false);
                                $('#categoryTable').DataTable().ajax.reload(null, false);
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
