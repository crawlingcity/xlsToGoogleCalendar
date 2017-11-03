@extends('layouts.app')

@section('content')
    <div class="hero-body">
        <form action="/upload" method="post">
            {{ csrf_field() }}
            <div class="container has-text-centered">
                <div class="column is-4 is-offset-4">
                    <h3 class="title has-text-grey">Actualizar Horário</h3>
                    <div class="field">
                        <div class="file is-centered is-boxed is-success has-name">
                            <label class="file-label">
                                <input id="fileupload" class="file-input" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="resume" data-url="/upload">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fa fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Carregar Excel
                                    </span>
                                    <span class="uploading">

                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(function () {
                $('#fileupload').fileupload({
                    dataType: 'json',
                    add: function (e, data) {
                        $.LoadingOverlay("show");
                        data.context = $('<p/>').text('Uploading...').appendTo($('.uploading'));
                        data.submit();
                    },
                    done: function (e, data) {
                        $('.uploading').html('Upload finished.');
                        $.LoadingOverlay("hide");
                    }
                });
            });
        });


    </script>
@stop
