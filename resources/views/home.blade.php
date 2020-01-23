@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="formWrapper" class="col-md-4 col-md-offset-4">
            <form class="form-vertical" role="form" enctype="multipart/form-data" method="post" action="http://localhost:8000/upload/images">
                {{csrf_field()}}
                @if(session()->has('status'))
                    <div class="alert alert-info" role="alert">
                        {{session()->get('status')}}
                    </div>
                @endif
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input type="text" name="name" class="form-control" id="name" value="">
                    @if($errors->has('name'))
                        <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input type="file" name="sliders[]" class="form-control" id="sliders" value="" multiple="true">
                    @if($errors->has('sliders'))
                        <span class="help-block">{{ $errors->first('sliders') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Upload Image </button>
                </div>
            </form>

        </div>
    </div>
    <!-- <div class="row" id="displayImages">
        @if($images)
            @foreach($images as $image)

                <div class="col-md-3">
                    <a href="{{$image->imgpath}}" target="_blank">
                        <img src="{{asset('uploads/'.$image->name)}}" class="img-responsive" alt="{{$image->name}}">
                    </a>
                </div>
            @endforeach
        @endif
    </div> -->
</div>
@endsection
