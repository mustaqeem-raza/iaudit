{{--
    Every message a page can show, written once for all pages:
    success and error flashes, and validation errors from any form on the page
    (the profile page uses named error bags, so all bags are listed).
    $errors is missing on an error page that runs before the session starts.
--}}
@if (session('success'))
    <div class="flash good" role="status">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="flash bad" role="alert">{{ session('error') }}</div>
@endif

@isset($errors)
    @php($messages = collect($errors->getBags())->flatMap(fn ($bag) => $bag->all()))

    @if ($messages->isNotEmpty())
        <div class="flash bad" role="alert">
            Please check the form:
            <ul>
                @foreach ($messages as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endisset
