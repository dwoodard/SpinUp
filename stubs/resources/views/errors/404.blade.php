<div class="flex">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <h1>404</h1>
        <h2>Page not found</h2>
        <p>Sorry, the page you are looking for could not be found.</p>
        <a href="{{ route('home') }}">Go back to the home page</a>

        <h2>{{ $exception->getMessage() }}</h2>
    </div>
</div>