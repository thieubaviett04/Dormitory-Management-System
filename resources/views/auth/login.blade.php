<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý KTX</title>
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

<body class="bg-muted/30 text-foreground min-h-screen flex flex-col justify-center items-center p-4 md:p-8 antialiased">

    <div class="w-full max-w-[400px] space-y-6">
        <!-- Logo and Heading -->
        <div class="flex flex-col items-center text-center space-y-3">
            <div class="bg-primary p-3 rounded-lg text-primary-foreground shadow-sm">
                <i data-lucide="building-2" class="h-6 w-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight">Hệ thống Quản lý KTX</h1>
                <p class="text-xs text-muted-foreground mt-1">Đăng nhập tài khoản cán bộ quản lý</p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-card text-card-foreground border border-border rounded-lg shadow-sm p-6 md:p-8 space-y-6">
            <h2 class="text-lg font-semibold tracking-tight text-center border-b border-border pb-3">Đăng nhập</h2>

            <!-- Errors Alert -->
            @if ($errors->any())
                <div class="bg-destructive/10 text-destructive border border-destructive/20 rounded-md p-3.5 text-xs flex items-start space-x-2">
                    <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 mt-0.5"></i>
                    <div>
                        <ul class="list-disc list-inside space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="space-y-1">
                    <label for="email" class="text-xs font-semibold text-muted-foreground">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground">
                            <i data-lucide="mail" class="h-4 w-4"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            placeholder="username@ktx.edu.vn"
                            class="w-full bg-background border border-input rounded-md pl-9 pr-4 py-2 text-sm placeholder:text-muted-foreground/60 focus:outline-none focus:ring-1 focus:ring-ring transition-shadow h-10">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1">
                    <label for="password" class="text-xs font-semibold text-muted-foreground">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground">
                            <i data-lucide="lock" class="h-4 w-4"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            placeholder="••••••••"
                            class="w-full bg-background border border-input rounded-md pl-9 pr-4 py-2 text-sm placeholder:text-muted-foreground/60 focus:outline-none focus:ring-1 focus:ring-ring transition-shadow h-10">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 text-xs font-medium select-none cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-input text-primary focus:ring-ring h-4 w-4">
                        <span class="text-muted-foreground hover:text-foreground transition-colors">Ghi nhớ đăng nhập</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-primary text-primary-foreground hover:bg-primary/95 h-10 px-4 py-2 rounded-md text-sm font-semibold tracking-wide transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    Đăng nhập
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-muted-foreground">
            Bản quyền &copy; {{ date('Y') }} Trường Đại học Thủy lợi.
        </p>
    </div>

    <!-- Initialize Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
