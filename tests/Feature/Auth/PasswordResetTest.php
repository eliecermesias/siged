<?php

test('Fortify password reset pages are available', function () {
    $this->get('/forgot-password')
        ->assertOk()
        ->assertSee('Forgot password');

    $this->get('/reset-password/sample-token')
        ->assertOk()
        ->assertSee('Reset password');
});
