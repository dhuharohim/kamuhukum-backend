@extends('masterpage')

@section('page_title')
    Users
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <div class="nftmax-table__heading">
            <h3 class="nftmax-table__title mb-0">User list</h3>
            <a href="{{ route('users-access.create') }}" class="btn btn-primary">Create</a>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless table-hover" id="userTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="">Username</th>
                        <th class="">Email</th>
                        <th class="">Role</th>
                        <th class=" text-end">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @foreach ($users as $index => $user)
                        <tr>
                            <td class="">{{ $user->username }}</td>
                            <td class="">
                                {{ $user->email }}
                            </td>
                            <td>
                                @foreach ($user->roles as $role)
                                    @php
                                        $badge = $role->name == 'author_' . $for ? 'primary' : 'dark';
                                        $name = $role->name == 'author_' . $for ? 'Author' : 'Editor';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $name }}</span>
                                @endforeach
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('users-access.show', $user->id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                    <a onclick="confirmDelete('{{ $user->id }}')" data-id="{{ $user->id }}"
                                        class="btn btn-outline-danger btn-sm">Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        let table = new DataTable('#userTable');

        function confirmDelete(id) {
            iziToast.question({
                timeout: 0,
                close: false,
                overlay: false,
                displayMode: 'once',
                id: 'question',
                zindex: 1,
                title: 'Are you sure?',
                message: 'Do you really want to delete this user?',
                position: 'center',
                buttons: [
                    ['<button><b>Yes</b></button>', function(instance, toast) {
                        // Perform the delete action here
                        deleteUser(id);

                        // Close the iziToast notification
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }, true],
                    ['<button>No</button>', function(instance, toast) {
                        // Just close the iziToast notification
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }]
                ]
            });
        }

        function deleteUser(id) {
            $.ajax({
                url: '/users-access/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    iziToast.success({
                        title: 'Deleted',
                        message: 'The item has been deleted successfully.',
                        position: 'topRight'
                    });
                    location.reload();
                },
                error: function(xhr, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: 'There was an error deleting the item.',
                        position: 'topRight'
                    });
                    location.reload();
                }
            });
        }
    </script>
@endsection
