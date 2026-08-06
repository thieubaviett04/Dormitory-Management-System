<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý KTX')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Shadcn CSS Variables & Tailwind Config -->
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96.1%;
            --secondary-foreground: 222.2 47.4% 11.2%;
            --muted: 210 40% 96.1%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96.1%;
            --accent-foreground: 222.2 47.4% 11.2%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        border: "hsl(var(--border))",
                        input: "hsl(var(--input))",
                        ring: "hsl(var(--ring))",
                        background: "hsl(var(--background))",
                        foreground: "hsl(var(--foreground))",
                        primary: {
                            DEFAULT: "hsl(var(--primary))",
                            foreground: "hsl(var(--primary-foreground))",
                        },
                        secondary: {
                            DEFAULT: "hsl(var(--secondary))",
                            foreground: "hsl(var(--secondary-foreground))",
                        },
                        destructive: {
                            DEFAULT: "hsl(var(--destructive))",
                            foreground: "hsl(var(--destructive-foreground))",
                        },
                        muted: {
                            DEFAULT: "hsl(var(--muted))",
                            foreground: "hsl(var(--muted-foreground))",
                        },
                        accent: {
                            DEFAULT: "hsl(var(--accent))",
                            foreground: "hsl(var(--accent-foreground))",
                        },
                        popover: {
                            DEFAULT: "hsl(var(--popover))",
                            foreground: "hsl(var(--popover-foreground))",
                        },
                        card: {
                            DEFAULT: "hsl(var(--card))",
                            foreground: "hsl(var(--card-foreground))",
                        },
                    },
                    borderRadius: {
                        lg: "var(--radius)",
                        md: "calc(var(--radius) - 2px)",
                        sm: "calc(var(--radius) - 4px)",
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-background text-foreground flex min-h-screen antialiased">

    <!-- Sidebar cố định -->
    <aside class="w-64 bg-card text-card-foreground flex flex-col shrink-0 border-r border-border">
        <!-- Logo Header -->
        <div class="h-16 flex items-center space-x-3 px-6 border-b border-border">
            <div class="bg-primary p-1.5 rounded-md text-primary-foreground">
                <i data-lucide="building-2" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold tracking-tight leading-none">Quản lý KTX</h2>
                <p class="text-[11px] text-muted-foreground mt-1">Hệ thống TLU</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5">
            <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-md text-muted-foreground hover:bg-accent hover:text-accent-foreground text-sm font-medium transition-colors">
                <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                <span>Tổng quan</span>
            </a>

            <a href="{{ route('invoice.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-md {{ Route::is('invoice.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} text-sm font-medium transition-colors">
                <i data-lucide="receipt" class="h-4 w-4"></i>
                <span>Điện & Nước</span>
            </a>

            <a href="{{ route('violation.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-md {{ Route::is('violation.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} text-sm font-medium transition-colors">
                <i data-lucide="alert-triangle" class="h-4 w-4"></i>
                <span>Vi phạm & Phạt</span>
            </a>

            <a href="{{ route('buildings.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-md {{ Route::is('buildings.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} text-sm font-medium transition-colors">
                <i data-lucide="building" class="h-4 w-4"></i>
                <span>Tòa nhà</span>
            </a>

            <a href="{{ route('rooms.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-md {{ Route::is('rooms.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} text-sm font-medium transition-colors">
                <i data-lucide="door-open" class="h-4 w-4"></i>
                <span>Phòng</span>
            </a>

            <a href="{{ route('beds.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-md {{ Route::is('beds.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} text-sm font-medium transition-colors">
                <i data-lucide="bed" class="h-4 w-4"></i>
                <span>Giường</span>
            </a>

            <a href="{{ route('contracts.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-md {{ Route::is('contracts.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} text-sm font-medium transition-colors">
                <i data-lucide="file-signature" class="h-4 w-4"></i>
                <span>Hợp đồng</span>
            </a>
        </nav>

        <!-- Footer Profile -->
        <div class="p-4 border-t border-border flex items-center justify-between text-xs">
            <div class="flex items-center space-x-3">
                <div class="bg-secondary h-8 w-8 rounded-full flex items-center justify-center text-secondary-foreground font-semibold border border-border">
                    A
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold leading-none">Admin User</span>
                    <span class="text-muted-foreground text-[11px] mt-1">admin@ktx.edu.vn</span>
                </div>
            </div>
            <button class="text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-accent transition-colors">
                <i data-lucide="log-out" class="h-4 w-4"></i>
            </button>
        </div>
    </aside>

    <!-- Content Wrapper layout -->
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
        <!-- Top Navbar -->
        <header class="bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 border-b border-border h-16 px-6 flex items-center justify-between shrink-0 sticky top-0 z-50">
            <!-- Breadcrumbs / Title area -->
            <div class="flex items-center space-x-2 text-sm font-medium text-muted-foreground">
                <a href="#" class="hover:text-foreground transition-colors"><i data-lucide="home" class="h-4 w-4"></i></a>
                <i data-lucide="chevron-right" class="h-4 w-4 text-border"></i>
                <span class="text-foreground">@yield('title', 'Quản lý')</span>
            </div>

            <!-- Navbar Actions -->
            <div class="flex items-center space-x-4">
                <!-- Search bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </div>
                    <input type="text" placeholder="Tìm kiếm..." class="w-64 bg-background border border-input rounded-md pl-9 pr-4 py-1.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring transition-shadow h-9">
                </div>

                <!-- Notifications -->
                <button class="relative p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground rounded-md transition-colors">
                    <i data-lucide="bell" class="h-5 w-5"></i>
                    <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-destructive rounded-full border-2 border-background"></span>
                </button>

                <!-- User Profile -->
                <button class="flex items-center space-x-2 p-1 pl-2 pr-2 rounded-md hover:bg-accent transition-colors border border-transparent hover:border-border">
                    <div class="h-7 w-7 bg-primary text-primary-foreground rounded-full flex items-center justify-center font-bold text-xs">
                        A
                    </div>
                    <span class="text-sm font-medium text-foreground">Admin</span>
                    <i data-lucide="chevron-down" class="h-4 w-4 text-muted-foreground"></i>
                </button>
            </div>
        </header>

        <!-- Main Page Area -->
        <main class="flex-1 p-8 overflow-y-auto bg-muted/30">
            <div class="w-full h-full">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
