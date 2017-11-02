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
                                <input id="file" class="file-input" type="file" name="resume" data-url="/upload">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fa fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Carregar Excel
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="file_ids" id="file_ids" value="" />
            <input type="submit" value="Upload" />
        </form>
    </div>
@endsection


