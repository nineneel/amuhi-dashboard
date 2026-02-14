<?php

it('forces dark mode on the login page', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('class="h-full dark"', false);
});

