{{-- Toastr messages --}}
<script>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
        toastr.error("{{ $error }}");
        @endforeach
    @elseif (session()->has('success'))
        toastr.success("{{ session()->get('success') }}");
    @elseif (session()->has('info'))
        toastr.success("{{ session()->get('info') }}");
    @endif
</script>