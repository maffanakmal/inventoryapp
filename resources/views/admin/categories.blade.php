@extends('admin.layout.main')

@section('child-content')
    <div class="header-wrapper d-flex justify-content-between align-items-center">
        <h3 class="mt-4">Categories</h3>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            Add Category
        </button>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatables" class="table table-stripped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 15px">No</th>
                            <th>Category Name</th>
                            <th class="text-center" style="width: 100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $category->category_name }}</td>
                                <td class="text-center">
                                    <a href="" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="categoryModalLabel">Add Category</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('categories.store') }}" method="POST" id="categoryForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-floating">
                            <input class="form-control" id="categoryName" name="categoryName" type="text" placeholder="Category Name" />
                            <label for="categoryName">Category Name</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#datatables').DataTable();
        });
    </script>
@endsection
