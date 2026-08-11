<x-mail::message>
# {{ ucfirst($period) }} Report Digest

Hi **{{ $userName }}**,

Your {{ $period }} sales and pipeline reports are attached.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
