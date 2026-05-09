<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Listing') }}
        </h2>
    </x-slot>

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css">

    <style>
        /* DataTables Tailwind-friendly tweaks */
        table.dataTable thead th {
            background: #f9fafb;
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #6366f1;
            outline: 2px solid rgba(99, 102, 241, 0.2);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem;
            margin: 0 2px;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb !important;
            background: white !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            color: white !important;
            border-color: #4f46e5 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #eef2ff !important;
            color: #4f46e5 !important;
        }

        /* Status badge */
        .status-badge {
            padding: 0.125rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-pending   { background: #fef3c7; color: #92400e; }

        /* Action menu - fixed positioning so it escapes table overflow */
        .action-menu-btn {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.375rem 0.5rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .action-menu-btn:hover { background: #e5e7eb; }

        .action-menu-portal {
            position: fixed;
            display: none;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            min-width: 180px;
            z-index: 9999;
            overflow: hidden;
        }
        .action-menu-portal.open { display: block; }
        .action-menu-portal a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            color: #374151;
            font-size: 0.875rem;
            text-decoration: none;
            transition: background 0.1s;
        }
        .action-menu-portal a:hover { background: #f3f4f6; color: #111827; }
        .action-menu-portal a svg { width: 16px; height: 16px; color: #6b7280; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table id="auditsTable" class="display nowrap stripe hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>User</th>
                                <th>Ship</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Date</th>
                                <th class="no-sort">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audits as $audit)
                                <tr>
                                    <td>{{ $audit->id }}</td>
                                    <td>{{ $audit->reference_number }}</td>
                                    <td>
                                        @if($audit->user)
                                            <div class="font-medium">{{ $audit->user->first_name }} {{ $audit->user->last_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $audit->user->email }}</div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($audit->ship)
                                            {{ $audit->ship->name }}
                                            <span class="text-xs text-gray-500">({{ $audit->ship->mnemonic }})</span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $audit->status }}">
                                            {{ ucfirst($audit->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $audit->score ?? '—' }}</td>
                                    <td data-order="{{ $audit->created_at->timestamp }}">
                                        {{ $audit->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td>
                                        <button type="button"
                                                class="action-menu-btn"
                                                data-audit-id="{{ $audit->id }}"
                                                data-plain-url="{{ route('audit.report.show', $audit->id) }}"
                                                data-pdf-url="{{ route('audit.report.pdf.show', $audit->id) }}"
                                                data-download-url="{{ route('audit.download', $audit->id) }}"
                                                aria-label="Actions">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Single shared dropdown rendered at body level (escapes overflow / viewport clipping) --}}
    <div id="actionMenuPortal" class="action-menu-portal" role="menu">
        <a href="#" data-action="plain">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Plain Report
        </a>
        <a href="#" data-action="pdf">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            PDF Report
        </a>
        <a href="#" data-action="download">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download
        </a>
    </div>

    {{-- DataTables JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialise DataTable
            const dt = new DataTable('#auditsTable', {
                pageLength: 15,
                order: [[6, 'desc']], // sort by Date desc by default
                columnDefs: [{ targets: 'no-sort', orderable: false }],
                language: {
                    search: '',
                    searchPlaceholder: 'Search audits…',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ audits',
                    infoEmpty: 'No audits found',
                    emptyTable: 'No audits found.',
                    zeroRecords: 'No audits match your search.',
                    paginate: { previous: '‹', next: '›' },
                },
                responsive: true,
            });

            // ---- Action dropdown: fixed-position, viewport-safe ----
            const portal = document.getElementById('actionMenuPortal');
            let activeBtn = null;

            function closeMenu() {
                portal.classList.remove('open');
                activeBtn = null;
            }

            function openMenuFor(btn) {
                activeBtn = btn;

                // Update links inside the portal
                portal.querySelector('[data-action="plain"]').href    = btn.dataset.plainUrl;
                portal.querySelector('[data-action="pdf"]').href      = btn.dataset.pdfUrl;
                portal.querySelector('[data-action="download"]').href = btn.dataset.downloadUrl;

                portal.classList.add('open');

                // Position relative to the button, flipping if it would overflow
                const rect       = btn.getBoundingClientRect();
                const portalRect = portal.getBoundingClientRect();
                const margin     = 4;

                let top  = rect.bottom + margin;
                let left = rect.right - portalRect.width;

                if (top + portalRect.height > window.innerHeight) {
                    top = rect.top - portalRect.height - margin; // flip up
                }
                if (left < 8) left = 8;
                if (left + portalRect.width > window.innerWidth - 8) {
                    left = window.innerWidth - portalRect.width - 8;
                }

                portal.style.top  = `${top}px`;
                portal.style.left = `${left}px`;
            }

            // Delegate clicks for any current/future row
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.action-menu-btn');
                if (btn) {
                    e.stopPropagation();
                    if (activeBtn === btn) {
                        closeMenu();
                    } else {
                        openMenuFor(btn);
                    }
                    return;
                }

                // Click outside the portal closes it
                if (!portal.contains(e.target)) {
                    closeMenu();
                }
            });

            // Close on scroll/resize/Esc to keep position correct
            window.addEventListener('scroll', closeMenu, true);
            window.addEventListener('resize', closeMenu);
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
        });
    </script>
</x-app-layout>
