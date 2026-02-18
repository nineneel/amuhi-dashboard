@props([
    'blocks' => [],
])

@php
    $resolvedBlocks = collect($blocks)
        ->map(function ($block) {
            if (! is_array($block)) {
                return null;
            }

            return [
                'type' => $block['type'] ?? 'paragraph',
                'text' => (string) ($block['text'] ?? ''),
                'items' => is_array($block['items'] ?? null) ? implode("\n", $block['items']) : (string) ($block['items'] ?? ''),
                'cite' => (string) ($block['cite'] ?? ''),
            ];
        })
        ->filter()
        ->values()
        ->all();
@endphp

<div x-data="{
    blocks: @js($resolvedBlocks),
    labels: {
        paragraphText: @js(__('ui.admin.paragraph_text')),
        headingText: @js(__('ui.admin.heading_text')),
        quoteText: @js(__('ui.admin.quote_text')),
    },
    addBlock(type = 'paragraph') {
        this.blocks.push({ type, text: '', items: '', cite: '' });
    },
    removeBlock(index) {
        this.blocks.splice(index, 1);
    }
}">
    <template x-if="blocks.length === 0">
        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center dark:border-gray-700 dark:bg-gray-900/30">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_content_blocks_yet_add_the_first_block_below') }}</p>
        </div>
    </template>

    <div class="space-y-4">
        <template x-for="(block, index) in blocks" :key="index">
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.admin.block_type') }}</label>
                        <select x-model="block.type" :name="`content[${index}][type]`"
                            class="h-9 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                            <option value="paragraph">{{ __('ui.admin.paragraph_block') }}</option>
                            <option value="heading">{{ __('ui.admin.heading_block') }}</option>
                            <option value="list">{{ __('ui.admin.list_block') }}</option>
                            <option value="quote">{{ __('ui.admin.quote_block') }}</option>
                        </select>
                    </div>

                    <button type="button" @click="removeBlock(index)"
                        class="inline-flex items-center rounded-lg border border-error-300 px-3 py-1.5 text-xs font-medium text-error-600 transition hover:bg-error-50 dark:border-error-500/50 dark:text-error-400 dark:hover:bg-error-500/10">
                        {{ __('ui.admin.remove') }}
                    </button>
                </div>

                <template x-if="block.type === 'list'">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.admin.list_items') }}</label>
                        <textarea rows="4" :name="`content[${index}][items]`" x-model="block.items"
                            placeholder="{{ __('ui.admin.write_one_item_per_line') }}"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"></textarea>
                        <p class="text-xs text-gray-400">{{ __('ui.admin.each_new_line_becomes_one_list_item') }}</p>
                    </div>
                </template>

                <template x-if="block.type !== 'list'">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            x-text="block.type === 'heading' ? labels.headingText : block.type === 'quote' ? labels.quoteText : labels.paragraphText"></label>
                        <textarea rows="4" :name="`content[${index}][text]`" x-model="block.text"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"></textarea>
                    </div>
                </template>

                <template x-if="block.type === 'quote'">
                    <div class="mt-3 space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.admin.citation') }}</label>
                        <input type="text" :name="`content[${index}][cite]`" x-model="block.cite"
                            placeholder="{{ __('ui.admin.source_speaker') }}"
                            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                </template>
            </div>
        </template>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" @click="addBlock('paragraph')"
            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
            {{ __('ui.admin.paragraph') }}
        </button>
        <button type="button" @click="addBlock('heading')"
            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
            {{ __('ui.admin.heading') }}
        </button>
        <button type="button" @click="addBlock('list')"
            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
            {{ __('ui.admin.list') }}
        </button>
        <button type="button" @click="addBlock('quote')"
            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
            {{ __('ui.admin.quote') }}
        </button>
    </div>
</div>
