@extends('layouts.main')

@section('title', 'Routes')

@section('content_header')
    <h1>Routes</h1>
@stop

@section('content')
    <div class="container">
        <h2>Laravel DataTables Tutorial Example</h2>
        <table class="table table-bordered" id="table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            </thead>
        </table>
    </div>
    <script>
        $(function() {
            $.noConflict();
            $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('route.list') }}',
            columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' }
                ]
            });
        });

        $.fn.dataTable.ext.errMode = 'throw';
    </script>
@stop
