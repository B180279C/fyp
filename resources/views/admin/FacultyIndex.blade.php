@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">Faculty Data</h3>
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
                <a href="{{route('faculty.create')}}" class="btn btn-primary">Add</a>
                <br>
                <br>
            </div>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Faculty Name</th>
                    <th>Action</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($facultys as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row['faculty_name']}}</td>
                    <td><a href="{{action('FacultyController@edit', $row['faculty_id'])}}">Edit</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
