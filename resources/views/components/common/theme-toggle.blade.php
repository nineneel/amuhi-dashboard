<button
    type="button"
    data-theme-toggle
    x-data="{
        theme: 'light',
        init() {
            this.theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
            this.applyTheme();
        },
        applyTheme() {
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        },
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            this.applyTheme();
        }
    }"
    @click="toggleTheme()"
    :aria-label="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
    class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
>
    <span class="sr-only" x-text="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"></span>

    <svg
        x-cloak
        x-show="theme === 'light'"
        class="h-5 w-5"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.8"
        />
    </svg>

    <svg
        x-cloak
        x-show="theme === 'dark'"
        class="h-5 w-5"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8" />
        <path
            d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H5M19 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-width="1.8"
        />
    </svg>
</button>
