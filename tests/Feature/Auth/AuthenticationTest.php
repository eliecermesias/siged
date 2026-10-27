<?php

test('Fortify login page is available and the home page links to it', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('login'))
        ->assertSee(route('register'));

    $this->get('/login')
        ->assertOk()
        ->assertSee('Log in to your account');
});
