<?php
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminTagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//  お問い合わせフォーム入力ページ
Route::get('/', [ContactController::class, 'index'])->name('contact.index');

//  お問い合わせフォーム確認ページ（※入力値のバリデーションを挟むためPOST
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');

// 送信処理
Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');

// サンクスページ（送信完了）
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');



// 管理画面関連（要認証：Fortifyよるログイン）

Route::middleware(['auth'])->group(function ()

{
    // 管理画面
    Route::get('/admin', [AdminContactController::class, 'index'])->name('admin.index');

    // エクスポート用
    Route::get('/contacts/export', [AdminContactController::class, 'export'])->name('admin.export');

    // お問い合わせ詳細ページ
    Route::get('/admin/contacts/{contact}', [AdminContactController::class, 'show'])->name('admin.show');

    // お問い合わせ削除処理
    Route::delete('/admin/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('admin.destroy');

    // お問い合わせタグ編集ページ
    Route::get('/admin/tags/{tag}/edit', [AdminTagController::class, 'edit'])->name('admin.tags.edit');
    
    // ▼ 【ここを追加】タグ新規登録処理 ▼
    Route::post('/admin/tags', [AdminTagController::class, 'store'])->name('admin.tags.store');

    // タグ更新処理
    Route::put('/admin/tags/{tag}', [AdminTagController::class, 'update'])->name('admin.tags.update');

    // タグ削除処理 
    Route::delete('/admin/tags/{tag}', [AdminTagController::class, 'destroy'])->name('admin.tags.destroy');

    // ログアウト後 /login へリダイレクトさせる場合
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
});









    });





