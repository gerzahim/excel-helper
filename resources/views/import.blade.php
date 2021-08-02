@extends('layout')

@section('content')
    <style>
        .uper {
            margin-top: 40px;
        }
    </style>
    <div class="card uper">
        <div class="card-header">
            CSV File Import Utility
        </div>
        <br><br><br>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br />
            @endif

            <form action="{{url('/processimport')}}" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    @csrf
                    <label for="name">Import File:</label>
                    <input type="file" name="imported-file"/>
                </div>
                <br><br>
                <button type="submit" class="btn btn-primary">Import</button>
            </form>
        </div>
    </div>
@endsection
