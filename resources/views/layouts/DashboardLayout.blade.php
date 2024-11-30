<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Background Job')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('mralgorithm') }}">Job Runner</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link 
                        @if (Request::is('mralgorithm/jobs')) active text-decoration-underline @endif"
                            href="{{ url('mralgorithm/jobs') }}">List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link
                        @if (Request::is('mralgorithm/jobs/create')) active text-decoration-underline @endif"
                            href="{{ url('mralgorithm/jobs/create') }}">Add</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">

        @if (Session::has('success'))
            <div class="alert alert-success" role="alert">
                <strong>Success!</strong> {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                <strong>Error!</strong> {{ Session::get('error') }}
            </div>
        @endif
        @if (count($errors) > 0)

            <div class="alert alert-danger" role="alert">
                <strong>Error(s)!</strong>
                <ol id="ul">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ol>
            </div>
        @endif
        @yield('content')
    </div>

<!-- delete record from list -->
<d  iv class="modal fade" style="z-index: 100000" id="delete_record" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
        <form id="delete_record_form" method="post">
            @method('delete')
            @csrf
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Confirmation</h3>
                        <p class="text-muted">Do you want to remove this file?</p>
                        <p class="text-muted"><b id="delete_record_message"></b></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-danger me-sm-3 me-1">Delete</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- delete record from list -->
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('script')

    <script>
function confirm_delete_recored(url, name) {
    document.getElementById('delete_record_form').setAttribute('action',url);
    document.getElementById('delete_record_message').innerHTML = name;
}
        </script>
</body>

</html>
