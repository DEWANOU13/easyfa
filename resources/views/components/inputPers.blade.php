<div class="relative">
    <input id="passwordInput"
        {{ $attributes->merge(['class' => 'block mt-1 w-full pr-10 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300']) }}
        type="password" />

    <button type="button" class="absolute inset-y-0 right-3 flex items-center text-gray-500" onclick="togglePassword()">
        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" title="Voir" viewBox="0 0 24 24"
            fill="none" stroke="currentColor">
            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path
                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z">
            </path>
        </svg>

        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" title="Voir" viewBox="0 0 24 24"
            fill="none" stroke="currentColor">
            <path d="M13.875 18.825A9.956 9.956 0 0112 19c-4.478 0-8.268-2.943-9.542-7A9.96 9.96 0 014.222 7.71"></path>
            <path d="M15 12a3 3 0 01-6 0m9.878-4.879L4.122 19.122"></path>
        </svg>
    </button>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('passwordInput');
        const eyeClosed = document.getElementById('eyeClosed');
        const eyeOpen = document.getElementById('eyeOpen');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeClosed.classList.add('hidden');
            eyeOpen.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeClosed.classList.remove('hidden');
            eyeOpen.classList.add('hidden');
        }
    }
</script>
