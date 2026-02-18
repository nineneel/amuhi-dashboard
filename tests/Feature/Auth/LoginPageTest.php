<?php

it('renders the login page with the new auth ui assets', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('auth-flare-orb')
        ->assertSee('authFlareFloat')
        ->assertSee('images/logo/amuhi-logo.png')
        ->assertSee(__('ui.buttons.sign_in'));
});
