<?php

it('rejects unsupported oauth providers', function () {
    $response = $this->get(route('social.redirect', 'unknown'));

    $response->assertNotFound();
});

it('handles oauth callback failures gracefully', function () {
    $response = $this->get(route('social.callback', 'google'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('oauth');
});
