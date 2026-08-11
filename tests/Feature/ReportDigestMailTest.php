<?php

use App\Mail\ReportDigestMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('renders report digest mailable', function (): void
{
    Mail::fake();

    $user = App\Models\User::factory()->create(['name' => 'BOSS']);

    Mail::to($user->email)->send(new ReportDigestMail(
        period: 'weekly',
        userName: $user->name,
        attachmentPath: null,
    ));

    Mail::assertSent(ReportDigestMail::class, 1);
});
