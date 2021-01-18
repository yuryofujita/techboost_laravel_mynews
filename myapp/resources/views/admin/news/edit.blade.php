@extends('layouts.admin')
@section('title', 'ニュースの編集')
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript">
$(function() {
  //テキストボックスのフォーカスが外れたら発動
  $('input[type="text"]').blur(function() {
      edit_ajax();
  });
  $('textarea').blur(function() {
      edit_ajax();
  });
  function edit_ajax() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    param = location.search;
    param_id = param.replace(/[^0-9]/g, '');
    $.ajax({
        //POST通信
        type: "POST",
        //ここでデータの送信先URLを指定します。
        url: "http://localhost/admin/news/update_ajax",
        data: {
            id:param_id,
            title: $("#title").val(),
            body: $("#body").val(),
        },
        //処理が成功したら
        success : function(data) {
            //HTMLファイル内の該当箇所にレスポンスデータを追加する場合
            console.log("更新成功しました。");
        },
        //処理がエラーであれば
        error : function() {
            alert('通信エラー');
        }
    });
  }
});
</script>

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h2>ニュース編集</h2>
                <form action="{{ action('Admin\NewsController@update') }}" method="post" enctype="multipart/form-data">
                    @if (count($errors) > 0)
                        <ul>
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="form-group row">
                        <label class="col-md-2" for="title">タイトル</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="title" class="event" name="title" value="{{ $news_form->title }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2" for="body">本文</label>
                        <div class="col-md-10">
                            <textarea class="form-control" id="body" class="event" name="body" rows="20">{{ $news_form->body }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2" for="image">画像</label>
                        <div class="col-md-10">
                            <input type="file" class="form-control-file" name="image">
                            <div class="form-text text-info">
                                設定中: {{ $news_form->image_path }}
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="remove" value="true">画像を削除
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-10">
                            <input type="hidden" name="id" value="{{ $news_form->id }}">
                            {{ csrf_field() }}
                            <input type="submit" class="btn btn-primary" value="更新">
                        </div>
                    </div>
                </form>
                <div class="row mt-5">
                    <div class="col-md-4 mx-auto">
                        <h2>編集履歴</h2>
                        <ul class="list-group">
                            @if ($news_form->histories != NULL)
                                @foreach ($news_form->histories as $history)
                                    <li class="list-group-item">{{ $history->edited_at }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
