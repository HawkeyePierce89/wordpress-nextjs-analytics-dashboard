'use client';

import { useFilters } from '@/hooks/use-filters';
import { useDebounce } from '@/hooks/use-debounce';
import { usePosts, useAuthors, useCategories } from '@/lib/queries';
import { PostsFilters } from './posts-filters';
import { PostsTable } from './posts-table';
import { PostsPagination } from './posts-pagination';

export function PostsListContent() {
  const { filters, setFilter, resetFilters } = useFilters();

  // Debounce search to avoid firing a request on every keystroke
  const debouncedSearch = useDebounce(filters.search, 300);
  const debouncedFilters = { ...filters, search: debouncedSearch };

  const { data, isLoading } = usePosts(debouncedFilters);
  const { data: authors = [] } = useAuthors();
  const { data: categories = [] } = useCategories();

  const posts = data?.items ?? [];
  const total = data?.total ?? 0;
  const totalPages = data?.totalPages ?? 1;

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-bold text-white">Posts</h1>
      </div>

      <PostsFilters
        filters={filters}
        setFilter={setFilter}
        resetFilters={resetFilters}
        authors={authors}
        categories={categories}
      />

      <PostsTable
        posts={posts}
        isLoading={isLoading}
        filters={filters}
        setFilter={setFilter}
      />

      {!isLoading && total > 0 && (
        <PostsPagination
          page={filters.page}
          totalPages={totalPages}
          total={total}
          perPage={filters.perPage}
          onPageChange={(page) => setFilter('page', page)}
        />
      )}
    </div>
  );
}
