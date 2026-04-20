@php($title = 'Login | Repertorio')
<x-layouts.app :title="$title">
    <section class="mx-auto mt-8 max-w-xl rounded-2xl border border-slate-200 bg-white/80 p-8 shadow-lg shadow-slate-200/40">
        <h1 class="text-2xl font-bold tracking-tight">Entrar no Repertorio</h1>
        <p class="mt-2 text-sm text-slate-600">
            Login exclusivo com Google via popup, sem redirecionamento externo.
        </p>

        @if ($googleClientId === '')
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                Configure GOOGLE_CLIENT_ID no arquivo .env para habilitar o login.
            </div>
        @else
            <div class="mt-6">
                <div id="google-login-button"></div>
                <p id="google-login-error" class="mt-3 hidden text-sm text-red-600"></p>
            </div>

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
                            theme: 'outline',
                            text: 'continue_with',
                            size: 'large',
                            width: 320,
                        }
                    );
                }
            </script>
        @endif
    </section>
</x-layouts.app>
