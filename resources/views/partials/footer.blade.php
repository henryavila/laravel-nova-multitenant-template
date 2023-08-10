<p class="mt-8 text-center text-xs text-80">
    &copy; {{ date('Y') }}  <span class="px-1">&middot;</span>
    {{ config('app.name')  }} v{{ \App\Helpers\AppVersion::appVersion() }} <br />
    Laravel v{{ app()->version()  }} |
    Nova v{{ \Laravel\Nova\Nova::version() }}
</p>
