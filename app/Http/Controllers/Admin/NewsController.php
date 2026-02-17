<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NewsStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsStoreRequest;
use App\Http\Requests\Admin\NewsUpdateRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $category = trim((string) $request->query('category', ''));

        $newsItems = News::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->when($category !== '', function ($query) use ($category): void {
                $query->where('category', $category);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.content.news.index', [
            'newsItems' => $newsItems,
            'statuses' => NewsStatus::cases(),
            'categories' => News::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
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
        $news = News::query()->create($this->preparePayload($request->validated()));

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'News article created successfully.');
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
        $news->update($this->preparePayload($request->validated()));

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'News article updated successfully.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'News article deleted successfully.');
    }

    public function publish(News $news): RedirectResponse
    {
        $news->update([
            'status' => NewsStatus::Published,
            'published_at' => $news->published_at ?? now(),
        ]);

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'News article published successfully.');
    }

    public function unpublish(News $news): RedirectResponse
    {
        $news->update([
            'status' => NewsStatus::Draft,
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'News article moved to draft.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function preparePayload(array $validated): array
    {
        $payload = $validated;
        $payload['tags'] = Arr::get($validated, 'tags', []);
        $payload['content'] = Arr::get($validated, 'content', []);
        $payload['related_slugs'] = Arr::get($validated, 'related_slugs', []);

        if (! array_key_exists('read_time_minutes', $payload) || $payload['read_time_minutes'] === null) {
            $payload['read_time_minutes'] = 5;
        }

        $status = $payload['status'] ?? NewsStatus::Draft->value;
        $payload['status'] = $status;

        if ($status === NewsStatus::Published->value && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        if ($status !== NewsStatus::Published->value) {
            $payload['published_at'] = null;
        }

        return $payload;
    }
}
