@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">Academic Data</h3>
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
                <a href="{{route('academic.create')}}" class="btn btn-primary">Add</a>
                <br>
                <br>
            </div>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Academic Name</th>
                    <th>Action</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($academic as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row['academic_name']}}</td>
                    <td><a href="{{action('AcademicController@edit', $row['academic_id'])}}">Edit</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
