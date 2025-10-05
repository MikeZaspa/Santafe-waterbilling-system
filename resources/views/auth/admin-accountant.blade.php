@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Manage Accountants</h4>
        <button class="btn btn-primary" id="btnAddAccountant">
            <i class="fas fa-plus"></i> Add Accountant
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search accountant...">
            </div>

            <table class="table table-bordered table-striped" id="accountantTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accountants as $index => $accountant)
                        <tr id="row_{{ $accountant->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $accountant->first_name }} {{ $accountant->last_name }}</td>
                            <td>{{ $accountant->email }}</td>
                            <td>{{ $accountant->contact }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editBtn" data-id="{{ $accountant->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $accountant->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="accountantModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="accountantForm">
            @csrf
            <input type="hidden" id="accountant_id" name="id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add Accountant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact</label>
                        <input type="text" class="form-control" name="contact" id="contact" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 🔍 Search Filter
    $('#search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#accountantTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // ➕ Add
    $('#btnAddAccountant').click(function() {
        $('#accountantForm')[0].reset();
        $('#accountant_id').val('');
        $('#modalTitle').text('Add Accountant');
        $('#accountantModal').modal('show');
    });

    // ✏️ Edit
    $(document).on('click', '.editBtn', function() {
        var id = $(this).data('id');
        $.get('/admin/accountants/' + id + '/edit', function(data) {
            $('#accountant_id').val(data.id);
            $('#first_name').val(data.first_name);
            $('#last_name').val(data.last_name);
            $('#email').val(data.email);
            $('#contact').val(data.contact);
            $('#modalTitle').text('Edit Accountant');
            $('#accountantModal').modal('show');
        });
    });

    // 💾 Save (Add or Update)
    $('#accountantForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var id = $('#accountant_id').val();

        $.ajax({
            url: id ? '/admin/accountants/' + id : '/admin/accountants',
            type: id ? 'PUT' : 'POST',
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                }).then(() => location.reload());
                $('#accountantModal').modal('hide');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Something went wrong.', 'error');
            }
        });
    });

    // ❌ Delete
    $(document).on('click', '.deleteBtn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Accountant?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/accountants/' + id,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        $('#row_' + id).remove();
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete accountant.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
