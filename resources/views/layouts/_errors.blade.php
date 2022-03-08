@if (session('success'))
    <div class="alert alert-success alert-dismissible">
        <i class="fa fa-check-circle-o" aria-hidden="true"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible">
        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif