<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if ($errors->any())
            Swal.fire({
                title: 'Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                icon: 'error'
            });
        @elseif (session('success'))
            Swal.fire({
                title: 'Success',
                text: @json(session('success')),
                icon: 'success'
            });
        @elseif (session('error'))
            Swal.fire({
                title: 'Error',
                text: @json(session('error')),
                icon: 'error'
            });
        @endif
    });
</script>