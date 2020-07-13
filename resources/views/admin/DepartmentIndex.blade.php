@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">Department Data</h3>
            <br>
            @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{$message}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div align="right">
                <a href="{{route('department.create')}}" class="btn btn-primary">Add</a>
                <br>
                <br>
            </div>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Faculty</th>
                    <th>Department Name</th>
                    <th>Action</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($departments as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row->faculty_name}}</td>
                    <td>{{$row->department_name}}</td>
                    <td><a href="{{action('DepartmentController@edit', $row->department_id)}}">Edit</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
