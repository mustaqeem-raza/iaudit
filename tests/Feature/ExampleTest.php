<?php

it('redirects the site root to the admin panel', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});
