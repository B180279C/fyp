@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">Student Data</h3>
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
                <a href="{{route('student.create')}}" class="btn btn-primary">Add</a>
                <br>
                <br>
            </div>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Programme</th>
                    <th>Batch</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($students as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row->name}}</td>
                    <td>{{$row->student_id}}</td>
                    <td>{{$row->programme_name}}</td>
                    <td>{{$row->short_form_name}}_{{$row->year}}_{{$row->semester}}{{$row->intake}}</td>
                    <td>{{$row->email}}</td>
                    <td><a href="{{action('StudentController@edit', $row->id)}}">Edit</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
