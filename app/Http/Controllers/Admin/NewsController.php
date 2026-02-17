<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NewsStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsStoreRequest;
use App\Http\Requests\Admin\NewsUpdateRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $category = (string) $request->query('category', '');

        $news = News::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('author_name', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = News::query()->distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.content.news.index', [
            'news' => $news,
            'statuses' => NewsStatus::cases(),
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.content.news.create', [
            'statuses' => NewsStatus::cases(),
        ]);
    }

    public function store(NewsStoreRequest $request): RedirectResponse
    {
        News::query()->create($request->validated());

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    public function show(News $news): View
    {
        return view('admin.content.news.show', [
            'news' => $news,
        ]);
    }

    public function edit(News $news): View
    {
        return view('admin.content.news.edit', [
            'news' => $news,
            'statuses' => NewsStatus::cases(),
        ]);
    }

    public function update(NewsUpdateRequest $request, News $news): RedirectResponse
    {
        $news->update($request->validated());

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    public function publish(News $news): RedirectResponse
    {
        $news->update([
            'status' => NewsStatus::Published,
            'published_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'News article published successfully.');
    }

    public function unpublish(News $news): RedirectResponse
    {
        $news->update([
            'status' => NewsStatus::Draft,
            'published_at' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'News article unpublished.');
    }
}
