<?php

test('Fortify registration page is available', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('Create an account');
});
