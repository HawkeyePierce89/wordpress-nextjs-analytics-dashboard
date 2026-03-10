'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import {
  useReactTable,
  getCoreRowModel,
  flexRender,
  type ColumnDef,
  type VisibilityState,
  type SortingState,
} from '@tanstack/react-table';
import type { Post, PostsFilterState } from '@/types';
import { StatusBadge } from '@/components/status-badge';
import { TableRowSkeleton } from '@/components/skeleton';
import { EmptyState } from '@/components/empty-state';

const VISIBILITY_STORAGE_KEY = 'posts-table-column-visibility';

function getEngagementColor(score: number): string {
  if (score >= 70) return 'text-green-400';
  if (score >= 50) return 'text-amber-400';
  return 'text-red-400';
}

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
}

type PostsTableProps = {
  posts: Post[];
  isLoading: boolean;
  filters: PostsFilterState;
  setFilter: <K extends keyof PostsFilterState>(key: K, value: PostsFilterState[K]) => void;
};

const columnDefs: ColumnDef<Post>[] = [
  {
    id: 'title',
    accessorKey: 'title',
    header: 'Title',
    cell: ({ getValue }) => (
      <span className="font-medium text-gray-100 line-clamp-1">
        {getValue() as string}
      </span>
    ),
  },
  {
    id: 'status',
    accessorKey: 'status',
    header: 'Status',
    cell: ({ getValue }) => <StatusBadge status={getValue() as Post['status']} />,
  },
  {
    id: 'author',
    accessorFn: (row) => row.author.name,
    header: 'Author',
    cell: ({ getValue }) => (
      <span className="text-gray-300">{getValue() as string}</span>
    ),
  },
  {
    id: 'categories',
    accessorFn: (row) => row.categories[0]?.name ?? '—',
    header: 'Category',
    cell: ({ getValue }) => {
      const cat = getValue() as string;
      if (cat === '—') return <span className="text-gray-500">—</span>;
      return (
        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-700 text-gray-300">
          {cat}
        </span>
      );
    },
  },
  {
    id: 'publishedAt',
    accessorKey: 'publishedAt',
    header: 'Published',
    cell: ({ getValue }) => (
      <span className="text-gray-400 text-xs">{formatDate(getValue() as string | null)}</span>
    ),
  },
  {
    id: 'readingTimeMinutes',
    accessorKey: 'readingTimeMinutes',
    header: 'Read Time',
    cell: ({ getValue }) => (
      <span className="text-gray-400">{getValue() as number} min</span>
    ),
  },
  {
    id: 'views',
    accessorFn: (row) => row.metrics.views,
    header: 'Views',
    cell: ({ getValue }) => {
      const v = getValue() as number | undefined;
      if (v == null) return <span className="text-gray-500">—</span>;
      return <span className="text-gray-300">{v.toLocaleString()}</span>;
    },
  },
  {
    id: 'engagementScore',
    accessorFn: (row) => row.metrics.engagementScore,
    header: 'Engagement',
    cell: ({ getValue }) => {
      const v = getValue() as number | undefined;
      if (v == null) return <span className="text-gray-500">—</span>;
      return (
        <span className={`font-medium ${getEngagementColor(v)}`}>
          {v.toFixed(1)}
        </span>
      );
    },
  },
];

const SORTABLE_COLUMNS: Record<string, PostsFilterState['sortBy']> = {
  title: 'title',
  views: 'views',
  engagementScore: 'engagement',
  publishedAt: 'date',
};

function getInitialVisibility(): VisibilityState {
  if (typeof window === 'undefined') return {};
  try {
    const stored = localStorage.getItem(VISIBILITY_STORAGE_KEY);
    if (stored) return JSON.parse(stored) as VisibilityState;
  } catch {
    // ignore
  }
  return {};
}

export function PostsTable({ posts, isLoading, filters, setFilter }: PostsTableProps) {
  const router = useRouter();
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>(
    getInitialVisibility
  );
  const [showVisibilityMenu, setShowVisibilityMenu] = useState(false);

  useEffect(() => {
    try {
      localStorage.setItem(VISIBILITY_STORAGE_KEY, JSON.stringify(columnVisibility));
    } catch {
      // ignore
    }
  }, [columnVisibility]);

  const sorting: SortingState = [
    {
      id:
        Object.entries(SORTABLE_COLUMNS).find(
          ([, v]) => v === filters.sortBy
        )?.[0] ?? 'publishedAt',
      desc: filters.sortOrder === 'desc',
    },
  ];

  const handleSortingChange = useCallback(
    (updater: SortingState | ((prev: SortingState) => SortingState)) => {
      const next = typeof updater === 'function' ? updater(sorting) : updater;
      if (next.length > 0) {
        const colId = next[0].id;
        const sortBy = SORTABLE_COLUMNS[colId];
        if (sortBy) {
          setFilter('sortBy', sortBy);
          setFilter('sortOrder', next[0].desc ? 'desc' : 'asc');
        }
      }
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [filters.sortBy, filters.sortOrder, setFilter]
  );

  const table = useReactTable({
    data: posts,
    columns: columnDefs,
    state: {
      columnVisibility,
      sorting,
    },
    onColumnVisibilityChange: setColumnVisibility,
    onSortingChange: handleSortingChange,
    getCoreRowModel: getCoreRowModel(),
    manualSorting: true,
  });

  const visibleColumns = table.getAllLeafColumns();

  return (
    <div className="relative">
      {/* Column visibility toggle */}
      <div className="flex justify-end mb-2">
        <div className="relative">
          <button
            onClick={() => setShowVisibilityMenu((v) => !v)}
            className="text-sm text-gray-400 hover:text-gray-200 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-md transition-colors"
          >
            Columns
          </button>
          {showVisibilityMenu && (
            <div className="absolute right-0 top-full mt-1 z-10 bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-3 min-w-[160px]">
              {visibleColumns.map((col) => (
                <label
                  key={col.id}
                  className="flex items-center gap-2 py-1 text-sm text-gray-300 hover:text-white cursor-pointer"
                >
                  <input
                    type="checkbox"
                    checked={col.getIsVisible()}
                    onChange={col.getToggleVisibilityHandler()}
                    className="rounded border-gray-600"
                  />
                  {typeof col.columnDef.header === 'string'
                    ? col.columnDef.header
                    : col.id}
                </label>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="overflow-x-auto rounded-lg border border-gray-700">
        <table className="w-full text-sm">
          <thead>
            {table.getHeaderGroups().map((headerGroup) => (
              <tr key={headerGroup.id} className="bg-gray-900 border-b border-gray-700">
                {headerGroup.headers.map((header) => {
                  const isSortable = !!SORTABLE_COLUMNS[header.column.id];
                  const sortDir = header.column.getIsSorted();
                  return (
                    <th
                      key={header.id}
                      className={`px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider select-none ${
                        isSortable ? 'cursor-pointer hover:text-gray-200' : ''
                      }`}
                      onClick={
                        isSortable
                          ? header.column.getToggleSortingHandler()
                          : undefined
                      }
                    >
                      <span className="inline-flex items-center gap-1">
                        {flexRender(
                          header.column.columnDef.header,
                          header.getContext()
                        )}
                        {isSortable && (
                          <span className="text-gray-600">
                            {sortDir === 'asc'
                              ? '↑'
                              : sortDir === 'desc'
                              ? '↓'
                              : '↕'}
                          </span>
                        )}
                      </span>
                    </th>
                  );
                })}
              </tr>
            ))}
          </thead>
          <tbody>
            {isLoading ? (
              Array.from({ length: 8 }).map((_, i) => (
                <TableRowSkeleton
                  key={i}
                  cols={table.getVisibleLeafColumns().length}
                />
              ))
            ) : table.getRowModel().rows.length === 0 ? (
              <tr>
                <td colSpan={table.getVisibleLeafColumns().length}>
                  <EmptyState message="No posts found. Try adjusting your filters." />
                </td>
              </tr>
            ) : (
              table.getRowModel().rows.map((row) => (
                <tr
                  key={row.id}
                  className="border-b border-gray-800 bg-gray-900 hover:bg-gray-800 cursor-pointer transition-colors"
                  onClick={() => router.push(`/posts/${row.original.id}`)}
                >
                  {row.getVisibleCells().map((cell) => (
                    <td key={cell.id} className="px-4 py-3">
                      {flexRender(
                        cell.column.columnDef.cell,
                        cell.getContext()
                      )}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
