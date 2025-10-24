@extends('admin.layout.main')

@section('child-content')
    <div class="header-wrapper d-flex justify-content-between align-items-center mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('master-data.products') }}" class="text-decoration-none text-primary">
                        Products
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Product Detail
                </li>
            </ol>
        </nav>
        <a href="{{ route('master-data.products') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex align-items-center">
            <h5 class="mb-0">Product Information</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold text-muted">Product Name</div>
                <div class="col-md-8">
                    <span class="text-dark">{{ $product->product_name }}</span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold text-muted">Category</div>
                <div class="col-md-8">
                    <span class="text-dark">{{ $product->category->category_name }}</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 fw-semibold text-muted">Description</div>
                <div class="col-md-8">
                    <p class="text-dark mb-0">{{ $product->product_description ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 fw-semibold">Product Variants</h5>
            <button onclick="variantModal()" class="btn btn-sm btn-primary mt-2 mt-sm-0">
                <i class="fa-solid fa-plus me-1"></i> Add Variant
            </button>
        </div>

        <div class="card-body">
            <div class="row mb-3 g-2 align-items-center">
                <div class="col-md-6 col-sm-12">
                    <div class="d-flex align-items-center">
                        <label for="showEntries" class="me-2 mb-0 text-muted">Show</label>
                        <select id="showEntries" class="form-select form-select-sm w-auto">
                            <option value="6" selected>6</option>
                            <option value="9">9</option>
                            <option value="12">12</option>
                            <option value="15">15</option>
                        </select>
                        <span class="ms-2 text-muted">entries</span>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="input-group input-group-sm">
                        <input type="text" id="searchVariant" class="form-control" placeholder="Search variant name...">
                        <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="variantList">
                @include('partials.variant-list', ['variants' => $product->variants])
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <small class="text-muted" id="variantCount"></small>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="variantPagination"></ul>
                </nav>
            </div>
        </div>
    </div>



    <div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="variantModalLabel">Tambah data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="variantForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="variant_edit" id="variant_edit">
                        <input type="hidden" name="product_id" id="product_id" value="{{ $product->product_id }}">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="variant_name" placeholder="Input Variant Name"
                                name="variant_name" value="{{ old('variant_name') }}">
                            <label for="variant_name" class="form-label">Variant Name</label>
                            <div class="invalid-feedback" id="error-variant_name"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="variant_price"
                                placeholder="Input Variant Price" name="variant_price"
                                value="{{ old('variant_price') }}">
                            <label for="variant_price" class="form-label">Variant Price</label>
                            <div class="invalid-feedback" id="error-variant_price"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="variant_stock"
                                placeholder="Input Variant Stock" name="variant_stock"
                                value="{{ old('variant_stock') }}">
                            <label for="variant_stock" class="form-label">Variant Stock</label>
                            <div class="invalid-feedback" id="error-variant_stock"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="variant_image" class="form-label">Variant Image</label>
                            <input type="file" class="form-control" id="variant_image"
                                placeholder="Input Variant Image" name="variant_image"
                                value="{{ old('variant_image') }}">
                            <div class="invalid-feedback" id="error-variant_image"></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="saveBtn" class="btn btn-primary btn-sm">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let product_id = {{ $product->product_id }};
        let currentPage = 1;

        function loadVariants(page = 1) {
            const search = $('#searchVariant').val();
            const limit = $('#showEntries').val();

            $.ajax({
                url: `/master-data/variant/${product_id}/list`,
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    limit: limit
                },
                success: function(response) {
                    $('#variantList').html(response.html);
                    $('#variantCount').text(
                        `Showing ${response.from} to ${response.to} of ${response.total} entries`);

                    let pagination = '';
                    for (let i = 1; i <= response.last_page; i++) {
                        pagination += `
                    <li class="page-item ${i === response.current_page ? 'active' : ''}">
                        <button class="page-link" onclick="loadVariants(${i})">${i}</button>
                    </li>`;
                    }
                    $('#variantPagination').html(pagination);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An unexpected error occurred while loading variants. Please try again later.",
                    });
                }
            });
        }

        $('#btnSearch').on('click', function() {
            loadVariants(1);
        });
        $('#showEntries').on('change', function() {
            loadVariants(1);
        });
        $('#searchVariant').on('keyup', function(e) {
            if (e.key === 'Enter') loadVariants(1);
        });

        $(document).ready(function() {
            loadVariants();
        });

        function variantModal() {
            $('#variantForm')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            method = 'create';
            variant_name;

            $('#variantModal').modal('show');
            $('#variantModalLabel').text('Add Variant');
            $('#saveBtn').text('Save');
        }

        $('#variantForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#saveBtn');
            btn.prop('disabled', true).html(
                '<div class="spinner-border spinner-border-sm text-light mb-0" role="status"><span class="visually-hidden">Loading...</span></div>'
            );

            const formData = new FormData(this);
            let url = '{{ route('master-data.variants.store') }}';
            let httpMethod = 'POST';

            if (method === 'update') {
                if (!variant_id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid variant ID.',
                    });
                    return;
                }

                url = '{{ route('master-data.variants.update', '') }}/' + variant_id;
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
                        $('#variantModal').modal('hide');
                        $('#variantForm').trigger('reset');

                        loadVariants();

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

        function editVariant(e) {
            variant_id = e.getAttribute('data-id');
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
                url: "{{ route('master-data.variants.show', '') }}/" + variant_id,
                type: "GET",
                success: function(response) {
                    btn.prop('disabled', false).html(method === 'update' ? 'Edit' : 'Save');

                    $('#variant_name').val(response.variant.variant_name);
                    $('#variant_price').val(response.variant.variant_price);
                    $('#variant_stock').val(response.variant.stock_quantity);
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

            $('#variantModal').modal('show');
            $('#variantModalLabel').text('Edit Variant');
            $('#saveBtn').text('Edit');
        }

        function deleteVariant(e) {
            const variant_id = e.getAttribute('data-id');

            Swal.fire({
                title: "Are you sure?",
                text: "Deleting selected variant cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('master-data.variants.delete', '') }}/" + variant_id,
                        type: "delete",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            loadVariants();
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
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
