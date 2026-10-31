<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teknologi Rekayasa Komputer')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>

<body class="font-sans antialiased bg-[#F9FAFB] text-[#111827]">

<div class="flex min-h-screen">
    @unless(View::hasSection('hide_sidebar'))
        @include('components.navbar') 
    @endunless

    <div class="flex min-w-0 flex-1 flex-col">
        @unless(View::hasSection('hide_header'))
            @include('components.header')
        @endunless

        <main class="flex-1 relative">
            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function syncGlobalAvatar() {
            const savedAvatar = localStorage.getItem('user_avatar_base64');
            if (savedAvatar) {
                const avatars = document.querySelectorAll('.global-avatar-sync');
                avatars.forEach(img => {
                    img.src = savedAvatar;
                    img.style.display = 'block';
                });
            }
        }
        syncGlobalAvatar();
        window.addEventListener('avatar-updated', syncGlobalAvatar);
    });
</script>

</body>
</html>