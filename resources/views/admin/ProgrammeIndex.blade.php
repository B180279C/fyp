@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">Programme Data</h3>
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
                <a href="{{route('programme.create')}}" class="btn btn-primary">Add</a>
                <br>
                <br>
            </div>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Programme Name</th>
                    <th>Department & Faculty</th>
                    <th>Level</th>
                    <th colspan="2"><center>Course</center></th>
                    <th>Action</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($programmes as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row->programme_name}}, ({{$row->short_form_name}})</td>
                    <td>{{$row->department_name}}, {{$row->faculty_name}}</td>
                    <td>{{$row->level}}</td>
                    <td><a href="{{action('SubjectController@create', $row->programme_id)}}">Subject List</a></td>
                    <td><a href="{{action('MPUController@view', $row->level)}}">MPU Subject List</a></td>
                    <td><a href="{{action('ProgrammeController@edit', $row->programme_id)}}">Edit</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
