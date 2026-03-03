<?php

it('renders the login page with the new auth ui assets', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('images/auth/amuhi-auth-background.mp4')
        ->assertSee('<video', false)
        ->assertSee('images/logo/amuhi-logo.png')
        ->assertSee(__('ui.buttons.sign_in'));
});

it('renders the register page with the same auth ui assets', function () {
    $this->get(route('register'))
        ->assertSuccessful()
        ->assertSee('images/auth/amuhi-auth-background.mp4')
        ->assertSee('<video', false)
        ->assertSee('images/logo/amuhi-logo.png')
        ->assertSee(__('ui.buttons.complete_registration'));
});

it('uses light theme by default for guests', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertDontSee('class="h-full dark"', false);
});

it('defines the updated light theme blue palette', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    expect($css)
        ->toContain('--color-brand-300: #95d9f0;')
        ->toContain('--color-brand-500: #2c58a8;');
});
