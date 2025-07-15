<?php

declare(strict_types=1);

it('example', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});
