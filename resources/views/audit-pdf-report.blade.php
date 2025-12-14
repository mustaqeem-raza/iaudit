<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Integrated Pest Management Audit Report</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#00838F", // Teal color from the logo/headers
                        secondary: "#E53935", // Red for the stamp
                        "background-light": "#FFFFFF",
                        "background-dark": "#1a202c", // Dark gray for dark mode
                        "text-light": "#1F2937", // Dark gray text
                        "text-dark": "#E5E7EB", // Light gray text
                        "teal-dark": "#004D40", // Darker teal for dark mode text
                        "teal-accent": "#26A69A", // Lighter teal for accents
                    },
                    fontFamily: {
                        display: ["Open Sans", "sans-serif"],
                        sans: ["Open Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                    backgroundImage: {
                        'wave-top': "url('https://cdn.jsdelivr.net/gh/benc-uk/workflow-dispatch@main/assets/wave-top.svg')", // Abstract placeholder for custom waves
                    }
                },
            },
        };
    </script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        .grunge-text {
            background: url('https://www.transparenttextures.com/patterns/concrete-wall.png');
            -webkit-background-clip: text;
            background-clip: text;
        }

        .custom-shape-divider-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 10;
        }

        .custom-shape-divider-top svg {
            position: relative;
            display: block;
            width: calc(150% + 1.3px);
            height: 120px;
        }

        .custom-shape-divider-top .shape-fill {
            fill: #00838F;
        }

        .custom-shape-divider-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 10;
        }

        .custom-shape-divider-bottom svg {
            position: relative;
            display: block;
            width: calc(140% + 1.3px);
            height: 80px;
        }

        .custom-shape-divider-bottom .shape-fill {
            fill: #00838F;
        }

        .page-container {
            min-height: 297mm;
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 font-display flex flex-col items-center min-h-screen py-8">
    <div
        class="page-container relative bg-background-light dark:bg-background-dark shadow-2xl overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 left-0 w-full h-32 z-0 pointer-events-none">
            <svg class="w-full h-full preserve-3d" viewBox="0 0 1440 320">
                <path
                    d="M0,96L80,112C160,128,320,160,480,149.3C640,139,800,85,960,80C1120,75,1280,117,1360,138.7L1440,160L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"
                    fill="#00838F" fill-opacity="1"></path>
                <path
                    d="M0,64L60,80C120,96,240,128,360,133.3C480,139,600,117,720,96C840,75,960,53,1080,53.3C1200,53,1320,75,1380,85.3L1440,96L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"
                    fill="#006064" fill-opacity="0.8"></path>
            </svg>
            <div class="absolute top-4 right-8 flex items-center gap-2">
                <span class="text-white text-3xl font-light tracking-wide">nutrastat</span>
                <div class="h-1 w-12 bg-white rounded-full mt-2"></div>
            </div>
        </div>
        <div class="relative z-10 flex-grow px-12 pt-40 pb-20 flex flex-col items-center text-center space-y-10">
            <div
                class="border-4 border-dashed border-red-600 dark:border-red-500 rounded-lg p-3 transform -rotate-1 opacity-90 mb-8 max-w-lg mx-auto">
                <h2 class="text-3xl md:text-4xl font-black text-red-600 dark:text-red-500 uppercase tracking-tighter leading-tight"
                    style="font-family: 'Courier New', Courier, monospace;">
                    Ship &amp; Head Office<br />Internal Use ONLY
                </h2>
            </div>
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-primary dark:text-teal-400">Integrated Pest Management</h1>
                <h1 class="text-3xl font-bold text-primary dark:text-teal-400">Audit Report</h1>
            </div>
            <div class="py-6">
                <h2 class="text-4xl font-extrabold text-primary dark:text-teal-400">Viking Mars (VOCX-MARS)</h2>
            </div>
            <div class="space-y-2 w-full">
                <h3 class="text-xl font-bold text-primary dark:text-teal-400">IPM Consultant</h3>
                <p class="text-lg font-bold text-gray-700 dark:text-gray-300">IPMConsultantName, IPMConsultantPosition
                </p>
            </div>
            <div class="py-4">
                <p class="italic text-primary font-semibold dark:text-teal-400">For and on behalf of Nutra Stat (UK)
                    Limited</p>
            </div>
            <div class="space-y-2 w-full">
                <h3 class="text-xl font-bold text-primary dark:text-teal-400">Audit Period</h3>
                <p class="text-lg font-bold text-gray-700 dark:text-gray-300">Saturday, 7 Jun 2025 to Saturday, 14 Jun
                    2025</p>
            </div>
            <div class="space-y-2 w-full">
                <h3 class="text-xl font-bold text-primary dark:text-teal-400">Audit Ports</h3>
                <p class="text-lg font-bold text-gray-700 dark:text-gray-300">PortFromHere to PortToHere</p>
            </div>
            <div class="space-y-2 w-full">
                <h3 class="text-xl font-bold text-primary dark:text-teal-400">Prepared For</h3>
                <p class="text-lg font-bold text-gray-700 dark:text-gray-300">PreparedForNameHere,
                    PreparedForPositionHere</p>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-24 z-0 pointer-events-none">
            <svg class="w-full h-full preserve-3d transform rotate-180" viewBox="0 0 1440 320">
                <path
                    d="M0,192L80,197.3C160,203,320,213,480,202.7C640,192,800,160,960,160C1120,160,1280,192,1360,208L1440,224L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"
                    fill="#00838F" fill-opacity="1"></path>
            </svg>
        </div>
        <div class="relative z-10 w-full px-8 pb-4 text-[10px] text-gray-600 dark:text-gray-400 leading-tight">
            <div class="grid grid-cols-[100px_1fr] gap-4">
                <div class="font-bold text-primary dark:text-teal-400 space-y-0.5">
                    <p>Office Address:</p>
                    <p>Contact Details:</p>
                    <p>Incorporated:</p>
                    <p>Registered office:</p>
                    <p>Prof. Association:</p>
                </div>
                <div class="space-y-0.5 font-semibold">
                    <p>Nutra Stat (UK) Limited, Hangar 7, Cecil Pashley Way, Shoreham Airport, Shoreham-by-Sea, West
                        Sussex, BN43 555</p>
                    <p>Telephone: +44 (0)7774 014896     Email: admin@nutrastat.com     Web: www.nutrastat.com</p>
                    <p>Companies House, Crown Way, Maindy, Cardiff, CF14 3UZ, United Kingdom (DX 33050)    Registration
                        No: 2894963</p>
                    <p>c/o Ayres Bright Vickers, Chartered Accountants, Bishopstoke, 36 Crescent Road, Worthing, West
                        Sussex, BN11 1RL, UK</p>
                    <p>National Pest Technicians Association, NPTA House, Hall Lance, Kinoulton, Nottingham, NG12 3EF
                        Member: 504</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
