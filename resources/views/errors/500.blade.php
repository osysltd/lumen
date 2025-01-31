<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="/favicon.ico" />
    <meta name="robots" content="noindex, nofollow, noarchive" />

    <meta name="author" content="{{ config('app.name') . ' ' . date('Y') }}" />
    <meta name="copyright" content="registered, delegated, verified {{ date('Y') }}" />
    <meta name="generator" content="{{ config('app.name') }}">
    <title>500 Internal Server Error - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="layoutError" style="display: flex; flex-direction: column; min-height: 100vh;">
        <div id="layoutError_content" style="min-width: 0; flex-grow: 1;">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="text-center mt-4">
                                <h1 class="display-1">500</h1>
                                <p class="lead">Internal Server Error</p>
                                <p>Access to this resource is not possible.</p>
                                <a href="/">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Return to {{ config('app.name') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutError_footer">
            <footer class="py-4 bg-dark mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyleft &copy; {{ config('app.name') . ' ' . date('Y') }}</div>
                        <div class="text-muted">
                            <a class="link-secondary" href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">Terms &amp; Conditions</a>
                            &otimes;
                            <a a class="link-secondary" href="https://www.commerce.gov/about/policies/privacy" target="_blank">Privacy Policy Adherence</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
