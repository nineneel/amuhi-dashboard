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
        $perPage = (int) $request->query('per_page', 15);

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
            ->paginate($perPage)
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
        $payload = $this->normalizeNewsPayload($request->validated(), (string) $request->input('tags_input', ''));

        News::query()->create($payload);

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('ui.admin.news_article_created_successfully'));
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
        $payload = $this->normalizeNewsPayload($request->validated(), (string) $request->input('tags_input', ''));

        $news->update($payload);

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('ui.admin.news_article_updated_successfully'));
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('ui.admin.news_article_deleted_successfully'));
    }

    public function publish(News $news): RedirectResponse
    {
        $news->update([
            'status' => NewsStatus::Published,
            'published_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', __('ui.admin.news_article_published_successfully'));
    }

    public function unpublish(News $news): RedirectResponse
    {
        $news->update([
            'status' => NewsStatus::Draft,
            'published_at' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', __('ui.admin.news_article_unpublished'));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeNewsPayload(array $payload, string $tagsInput = ''): array
    {
        if ($tagsInput !== '') {
            $payload['tags'] = $this->parseCommaSeparatedValues($tagsInput);
        }

        $payload['content'] = collect($payload['content'] ?? [])
            ->map(function ($block): array {
                if (! is_array($block)) {
                    return [
                        'type' => 'paragraph',
                        'text' => (string) $block,
                    ];
                }

                $type = (string) ($block['type'] ?? 'paragraph');

                if ($type === 'list') {
                    $rawItems = $block['items'] ?? [];
                    $items = is_array($rawItems)
                        ? $rawItems
                        : preg_split('/\r\n|\r|\n/', (string) $rawItems);

                    return [
                        'type' => 'list',
                        'items' => collect($items)->map(fn ($item) => trim((string) $item))->filter()->values()->all(),
                    ];
                }

                $normalized = [
                    'type' => $type,
                    'text' => trim((string) ($block['text'] ?? '')),
                ];

                if ($type === 'quote') {
                    $normalized['cite'] = trim((string) ($block['cite'] ?? ''));
                }

                return $normalized;
            })
            ->values()
            ->all();

        return $payload;
    }

    /**
     * @return list<string>
     */
    private function parseCommaSeparatedValues(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
