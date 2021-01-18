<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\News;
use App\History;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function add()
    {
        return view('admin.news.create');
    }

    private $hoge = 1;
    public function create(Request $request)
    {
        //フォームの内容を書き換える場合
        // $request->merge(['title(タイトル)'=>$request->input('title')]);
        // $request->merge(['body(本文)'=>$request->input('body')]);
        // $request->request->remove('title');
        // $request->request->remove('body');

        $this->validate($request, News::$rules);
        //test追加
        echo "test1tuika";

        $news = new News;
        // dd($request);
        $form = $request->all();
        // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
        if (isset($form['image'])) {
            $path = $request->file('image')->store('public/image');
            $news->image_path = basename($path);
        } else {
            $news->image_path = null;
        }

        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        // フォームから送信されてきたimageを削除する
        unset($form['image']);
        // データベースに保存する
        $news->fill($form);
        $news->save();

        // admin/news/createにリダイレクトする
        return redirect('admin/news');
    }

    public function index(Request $request)
    {
        //セッション格納
        //session()->put(['name' => '氏名', 'address' => '東京']);
        //dd(session()->all());
       
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
            // 検索されたら検索結果を取得する
            //$posts = News::where('title', $cond_title)->get();
            //あいまい検索
            $posts = News::where('title', 'like' , '%' . $cond_title . '%')->get();
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = News::all();

            \Log::debug($posts);
            //親クラスのメソッドを呼び出し
            $this->test_oya();
        }
        $aaa = $this->getNews($this->hoge);
        return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]);
    }

    private function getNews() {
        return News::all();
    }

    // 以下を追記
    public function edit(Request $request)
    {
        // News Modelからデータを取得する
        $news = News::find($request->id);
        if (empty($news)) {
            abort(404);
        }
        return view('admin.news.edit', ['news_form' => $news]);
    }

    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, News::$rules);
        // News Modelからデータを取得する
        $news = News::find($request->id);
        // 送信されてきたフォームデータを格納する
        $news_form = $request->all();
        if (isset($news_form['image'])) {
            $path = $request->file('image')->store('public/image');
            $news->image_path = basename($path);
            unset($news_form['image']);
        } elseif (isset($request->remove)) {
            $news->image_path = null;
            unset($news_form['remove']);
        }
        unset($news_form['_token']);
        // 該当するデータを上書きして保存する

        \DB::beginTransaction();    //トランザクション開始
        try {
            $news->fill($news_form)->save();

            $history = new History;
            $history->news_id = $news->id;
            $history->edited_at = Carbon::now();
            $history->save();
            if (false) {
                throw new \Exception('意図的にエラー');
            }
            \DB::commit();    //DB更新を反映
        } catch (\Exception $e) {
            \DB::rollback();    //DB更新を反映しない
            \Session::flash('error_message', $e->getMessage());
            return redirect('admin/news/edit?id='.$news->id);
        }

        \Session::flash('flash_message', '更新に成功しました。');
        return redirect('admin/news');
    }

    //編集画面のajax可
    public function update_ajax(Request $request)
    {
        \Log::debug($request);
        $news = News::find($request->id);
        $news_form = $request->all();
        $news->fill($news_form)->save();
    }

    public function delete(Request $request)
    {
        // 該当するNews Modelを取得
        $news = News::find($request->id);
        // 削除する
        $news->delete();

        //[追加]紐づくhistoriesもdelete
        History::where('news_id', $request->id)->delete();

        //TODO:画像も削除できた方がbetter

        return redirect('admin/news/');
    }
}
