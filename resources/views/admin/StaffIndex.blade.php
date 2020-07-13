@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">Staff Data</h3>
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
                <a href="{{route('staff.create')}}" class="btn btn-primary">Add</a>
                <br>
                <br>
            </div>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Name</th>
                    <th>Staff Id</th>
                    <th>Position</th>
                    <th>Email</th>
                    <th colspan="2">Action</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($staffs as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row->name}}</td>
                    <td>{{$row->staff_id}}</td>
                    <td>{{$row->position}} , {{$row->department_name}}</td>
                    <td>{{$row->email}}</td>
                    <td><a href="{{action('StaffController@edit', $row->staff_id)}}">Edit</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
