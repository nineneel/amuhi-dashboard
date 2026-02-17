@php
    use App\Helpers\AdminMenuHelper;

    $menuGroups = AdminMenuHelper::getMenuGroups(auth()->user());
    $currentPath = request()->path();
@endphp

<aside id="admin-sidebar"
    class="fixed left-0 top-0 z-99999 mt-0 flex h-screen w-[90px] flex-col border-r border-gray-200 bg-white px-5 text-gray-900 transition-all duration-300 ease-in-out [.sidebar-expanded_&]:min-w-[290px] dark:border-gray-800 dark:bg-gray-900"
    x-data="{
        isActive(path) {
            return window.location.pathname === path || '{{ $currentPath }}' === path.replace(/^\//, '');
        }
    }"
    :class="{
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <div class="flex pb-7 pt-8 pl-6 xl:justify-center xl:pl-0 [.sidebar-expanded_&]:justify-start [.sidebar-expanded_&]:xl:pl-6">
        <a href="{{ route('admin.dashboard') }}">
            <div class="hidden [.sidebar-expanded_&]:block">
                <img class="dark:hidden" src="/images/logo/logo.png" alt="AMUHI" width="150" height="40" />
                <img class="hidden dark:block dark:brightness-0 dark:invert" src="/images/logo/logo-dark.png" alt="AMUHI"
                    width="150" height="40" />
            </div>
            <img class="block [.sidebar-expanded_&]:hidden" src="/images/logo/logo-icon.png" alt="AMUHI" width="32"
                height="32" />
        </a>
    </div>

    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                @foreach ($menuGroups as $menuGroup)
                    <div>
                        <h2 class="mb-4 flex text-xs leading-[20px] text-gray-400 uppercase"
                            :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                            <template
                                x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                                <span>{{ $menuGroup['title'] }}</span>
                            </template>
                            <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                        fill="currentColor" />
                                </svg>
                            </template>
                        </h2>

                        <ul class="flex flex-col gap-1">
                            @foreach ($menuGroup['items'] as $item)
                                <li>
                                    <a href="{{ $item['path'] }}" class="menu-item group"
                                        :class="[
                                            isActive('{{ $item['path'] }}') ? 'menu-item-active' : 'menu-item-inactive',
                                            (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                                            'xl:justify-center' :
                                            'justify-start'
                                        ]">
                                        <span
                                            :class="isActive('{{ $item['path'] }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                            {!! AdminMenuHelper::getIconSvg($item['icon']) !!}
                                        </span>

                                        <span
                                            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                            class="menu-item-text flex items-center gap-2">
                                            {{ $item['name'] }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </nav>
    </div>
</aside>

<div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.setMobileOpen(false)"
    class="fixed z-50 h-screen w-full bg-gray-900/50" x-cloak></div>
