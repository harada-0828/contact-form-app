<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['category', 'tags']);

        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $contacts = $query->latest()->paginate(7)->appends($request->query());

        $categories = Category::all();

        $tags = Tag::all();

        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.index')->with('success', 'お問い合わせを削除しました。');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Contact::with(['category', 'tags']);

        // indexと同じ検索条件を反映
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $contacts = $query->latest()->get();

        $callback = function () use ($contacts) {
            $file = fopen('php://output', 'w');

            // 文字化け防止のBOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ヘッダー行
            fputcsv($file, ['ID', '姓', '名', '性別', 'メールアドレス', '電話番号', '住所', '建物名', 'カテゴリ', '内容', '作成日時']);

            foreach ($contacts as $contact) {
                $genderText = match ((int) $contact->gender) {
                    1 => '男性',
                    2 => '女性',
                    3 => 'その他',
                    default => '未設定',
                };

                fputcsv($file, [
                    $contact->id,
                    $contact->first_name,
                    $contact->last_name,
                    $genderText,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    optional($contact->category)->content,
                    $contact->detail,
                    $contact->created_at,
                ]);
            }

            fclose($file);
        };

        $filename = 'contacts_'.date('Ymd_His').'.csv';

        return response()->stream($callback, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
