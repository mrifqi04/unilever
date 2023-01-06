@push('styles')
<link href="{{asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
<script src="{{asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script>
    $('document').ready(function(){
        @if (\Session::has('message.level'))
            swal({
                type: "{{ \Session::get('message.level') }}",
                title: "{{ \Session::get('message.title') }}",
                text: "{{ \Session::get('message.data') }}",
                timer: 3000,
            });
        @endif
    });
</script>
@endpush