@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-8 offset-md-2">

            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="glyphicon glyphicon-edit"></i> 编辑个人资料
                    </h4>
                </div>

                <div class="card-body">
                    @include('shared._errors')
                    <form action="{{ route('users.update', $user->id) }}" method="POST" accept-charset="UTF-8"  enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label for="name-field">用户名</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name-field" value="{{ old('name', $user->name) }}" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email-field">邮 箱</label>
                            <input class="form-control @error('email') is-invalid @enderror" type="text" name="email" id="email-field" value="{{ old('email', $user->email) }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="introduction-field">个人简介</label>
                            <textarea name="introduction" id="introduction-field" class="form-control @error('introduction') is-invalid @enderror" rows="3">{{ old('introduction', $user->introduction) }}</textarea>
                            @error('introduction')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="" class="avatar-label">用户头像</label>
                            <input type="file" class="form-control-file" name="avatar">
                        </div>

                        @if($user->avatar)
                            <br>
                            <img class="thumbnail img-responsive" src="{{ is_url($user->avatar) ? $user->avatar : url($user->avatar) }}" width="200" />
                        @endif
                        <div class="well well-sm">
                            <button type="submit" class="btn btn-primary">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
