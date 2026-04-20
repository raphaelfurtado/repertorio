@php($title = 'Login | Repertorio')
<x-layouts.app :title="$title">
    <section class="rp-glass rounded-3xl p-7 sm:p-8">
        <div class="mb-7 text-center">
            <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/25 text-xl font-black text-indigo-100">R</div>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-indigo-100">Repertorio Pro</h1>
            <p class="mt-2 text-sm text-slate-400">Professional Repertoire Management</p>
        </div>

        <div class="rp-card rounded-2xl p-5">
            <h2 class="text-xl font-bold text-indigo-50">Welcome Back</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-400">
                Sign in to access your setlists, library, and performance gear. Simple, fast and secure.
            </p>

            @if ($googleClientId === '')
                <div class="mt-6 rounded-xl border border-red-400/40 bg-red-400/10 p-4 text-sm text-red-200">
                    Configure GOOGLE_CLIENT_ID no .env para habilitar o login com Google.
                </div>
            @else
                <div class="mt-6 flex justify-center">
                    <div id="google-login-button"></div>
                </div>
                <p id="google-login-error" class="mt-3 hidden text-center text-sm text-red-300"></p>

                <script src="https://accounts.google.com/gsi/client" async defer></script>
                <script>
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const errorEl = document.getElementById('google-login-error');

                    window.handleGoogleCredentialResponse = async function (response) {
                        errorEl.classList.add('hidden');

                        const request = await fetch('{{ route('auth.google') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ credential: response.credential }),
                        });

                        if (request.ok) {
                            const data = await request.json();
                            window.location.href = data.redirect;
                            return;
                        }

                        const data = await request.json().catch(() => ({}));
                        const msg = data?.message || 'Falha ao autenticar com Google.';
                        errorEl.textContent = msg;
                        errorEl.classList.remove('hidden');
                    }

                    window.onload = function () {
                        google.accounts.id.initialize({
                            client_id: '{{ $googleClientId }}',
                            callback: handleGoogleCredentialResponse,
                            ux_mode: 'popup',
                        });

                        google.accounts.id.renderButton(
                            document.getElementById('google-login-button'),
                            {
                                type: 'standard',
                                shape: 'pill',
                                theme: 'filled_blue',
                                text: 'continue_with',
                                size: 'large',
                                width: 320,
                            }
                        );
                    }
                </script>
            @endif

            <div class="mt-6 text-center text-xs text-slate-500">
                Encrypted with stage-grade security protocols.
            </div>
        </div>

        <div class="mt-5 text-center text-xs text-slate-500">
            Privacy Policy · Terms of Service · Help Center
        </div>
    </section>
</x-layouts.app>
