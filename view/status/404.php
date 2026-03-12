<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>hr · flow · tasks </title>
        <link rel="stylesheet" href="/public/assets/css/output.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <body
        class="min-h-screen bg-linear-to-br from-slate-950 via-indigo-950 to-purple-950 flex items-center justify-center p-4 relative overflow-x-hidden">

        <!-- decorative stars / cosmic dust (pure css) -->
        <div class="fixed inset-0 pointer-events-none">
            <div class="star w-1 h-1 left-[15%] top-[23%]"></div>
            <div class="star w-0.75 h-0.75 left-[72%] top-[12%] animate-pulse"></div>
            <div class="star w-0.5 h-0.5 left-[34%] top-[85%]"></div>
            <div class="star w-1 h-1 left-[88%] top-[42%]"></div>
            <div class="star w-0.5 h-0.5 left-[8%] top-[71%]"></div>
            <div class="star w-1 h-1 left-[53%] top-[34%] opacity-40 blur-0.5"></div>
            <div class="star w-0.5 h-0.5 left-[93%] top-[77%]"></div>
            <div class="star w-1 h-1 left-[43%] top-[18%]"></div>
        </div>

        <!-- main floating card / planet-ish container -->
        <div
            class="relative max-w-3xl w-full backdrop-blur-sm bg-white/5 border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl shadow-indigo-500/20 animate-float">

            <!-- 404 huge display with linear and glow -->
            <div class="text-center mb-8 select-none">
                <span
                    class="text-8xl md:text-9xl font-black bg-linear-to-r from-blue-400 via-fuchsia-300 to-indigo-400 bg-clip-text text-transparent glow inline-block">
                    404
                </span>
            </div>

            <!-- friendly message -->
            <h1 class="text-3xl md:text-4xl font-bold text-white text-center tracking-tight">
                lost in the <span
                    class="bg-linear-to-r from-amber-200 to-yellow-400 bg-clip-text text-transparent">cosmos</span>
            </h1>

            <!-- two action buttons with soft glow -->
            <div class="flex flex-col sm:flex-row gap-5 justify-center mt-10">
                <a href=""
                    class="group relative px-8 py-4 rounded-xl bg-linear-to-r from-blue-600 to-indigo-600 text-white font-semibold text-lg shadow-lg shadow-blue-600/30 hover:shadow-blue-500/50 transition-all duration-300 hover:-translate-y-1 hover:scale-105 border border-white/20">
                    <span class="relative z-10">🚀 mission control</span>
                    <span
                        class="absolute inset-0 rounded-xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity blur-xl"></span>
                </a>
                <a href="/"
                    class="group relative px-8 py-4 rounded-xl bg-white/10 backdrop-blur-sm text-white font-semibold text-lg border border-white/20 shadow-lg hover:bg-white/20 transition-all duration-300 hover:-translate-y-1 hover:scale-105">
                    <span class="relative z-10">✨ explore home</span>
                    <span
                        class="absolute inset-0 rounded-xl bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity blur-md"></span>
                </a>
            </div>

            <div class="flex items-center justify-center gap-2 mt-12 text-indigo-300/60 text-sm">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </span>
                <span> We're fine — but this page is in another galaxy</span>
            </div>
        </div>

        <!-- extra decorative floating orbs / planets (absolute) -->
        <div
            class="fixed left-[5%] bottom-[10%] w-32 h-32 rounded-full bg-linear-to-t from-purple-800/20 to-fuchsia-600/10 blur-3xl -z-10">
        </div>
        <div
            class="fixed right-[3%] top-[15%] w-48 h-48 rounded-full bg-linear-to-b from-blue-800/10 to-indigo-900/20 blur-3xl -z-10">
        </div>

        <!-- micro interaction: tiny planet with ring (svg?) but using pure css is fine -->
        <div
            class="fixed left-[10%] top-[20%] w-1 h-1 bg-white/30 rounded-full shadow-[0_0_15px_3px_rgba(255,255,255,0.3)]">
        </div>
        <div class="fixed right-[15%] bottom-[25%] w-2 h-2 bg-indigo-300/40 rounded-full blur-sm"></div>

        <!-- hidden text for screen readers (a11y) -->
        <div class="sr-only">404 error, page not found. return to home or mission control.</div>
    </body>

</html>