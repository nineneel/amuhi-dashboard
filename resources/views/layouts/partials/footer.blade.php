<footer class="footer {{ $footerClass ?? '' }}">
    <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
        <span>Copyright &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
    </p>
    <div class="d-flex align-items-center gap-4">
        <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Help</a>
        <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Terms</a>
        <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacy</a>
    </div>
</footer>
