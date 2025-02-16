@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dashboard</h2>

    <!-- Form Create Post -->
    <div class="card">
        <div class="card-header">Create New Post</div>
        <div class="card-body">
            <form id="createPostForm">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Title:</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content:</label>
                    <textarea id="content" name="content" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Post</button>
            </form>
        </div>
    </div>

    <!-- Alert Message -->
    <div id="alertMessage" class="alert alert-success mt-3 d-none"></div>

    <!-- Daftar Post -->
    <h3 class="mt-4">Your Posts</h3>
    <div id="postsContainer">
        @foreach ($posts as $post)
            <div class="card mt-3 post-item" id="post-{{ $post->id }}">
                <div class="card-body">
                    <h5>{{ $post->title }}</h5>
                    <p>{{ $post->content }}</p>
                    <button class="btn btn-danger deletePost" data-id="{{ $post->id }}">Delete</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Create Post using AJAX
        $('#createPostForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('dashboard.posts.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    title: $('#title').val(),
                    content: $('#content').val(),
                },
                success: function (response) {
                    if (response.success) {
                        // Tampilkan pesan sukses
                        $('#alertMessage').removeClass('d-none').text(response.message);

                        // Tambahkan post baru ke daftar tanpa reload
                        $('#postsContainer').prepend(`
                            <div class="card mt-3 post-item" id="post-${response.post.id}">
                                <div class="card-body">
                                    <h5>${response.post.title}</h5>
                                    <p>${response.post.content}</p>
                                    <button class="btn btn-danger deletePost" data-id="${response.post.id}">Delete</button>
                                </div>
                            </div>
                        `);

                        // Reset form
                        $('#title').val('');
                        $('#content').val('');
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        // Delete Post using AJAX
        $(document).on('click', '.deletePost', function () {
    let postId = $(this).data('id');
    let postCard = $('#post-' + postId);

    if (confirm('Are you sure you want to delete this post?')) {
        $.ajax({
            url: "/dashboard/posts/destroy/" + postId,
            method: "POST",  // <-- Perbaiki menjadi DELETE
            data: {
                _method: "DELETE",  // <-- Tambahkan ini untuk metode DELETE
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                if (response.success) {
                    postCard.fadeOut(500, function () {
                        $(this).remove();
                    });
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }
});

</script>

@endsection
