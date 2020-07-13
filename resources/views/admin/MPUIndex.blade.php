@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br>
            <h3 align="center">General Studies Data</h3>
            <br>
            @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{$message}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <table class="table table-bordered table-striped">
                <tr>
                    <th>No. </th>
                    <th>Level</th>
                    <th>Course</th>
                </tr>
                <?php
                $i = 1; 
                ?>
                @foreach($programmes as $row)
                <tr>
                    <td><?php echo $i++?></td>
                    <td>{{$row->level}}</td>
                    <td><a href="{{action('MPUController@create', $row->level)}}">MPU Subject List</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
