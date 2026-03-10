'use client';

import { useCallback } from 'react';
import { useRouter, usePathname, useSearchParams } from 'next/navigation';
import type { PostsFilterState, PostStatus } from '@/types';

const DEFAULT_FILTERS: PostsFilterState = {
  search: '',
  status: '',
  authorId: '',
  categoryId: '',
  page: 1,
  perPage: 20,
  sortBy: 'date',
  sortOrder: 'desc',
};

function getFiltersFromParams(params: URLSearchParams): PostsFilterState {
  return {
    search: params.get('search') ?? DEFAULT_FILTERS.search,
    status: (params.get('status') as PostStatus | '') ?? DEFAULT_FILTERS.status,
    authorId: params.get('authorId') ? Number(params.get('authorId')) : DEFAULT_FILTERS.authorId,
    categoryId: params.get('categoryId') ? Number(params.get('categoryId')) : DEFAULT_FILTERS.categoryId,
    page: params.get('page') ? Number(params.get('page')) : DEFAULT_FILTERS.page,
    perPage: params.get('perPage') ? Number(params.get('perPage')) : DEFAULT_FILTERS.perPage,
    sortBy: (params.get('sortBy') as PostsFilterState['sortBy']) ?? DEFAULT_FILTERS.sortBy,
    sortOrder: (params.get('sortOrder') as PostsFilterState['sortOrder']) ?? DEFAULT_FILTERS.sortOrder,
  };
}

type FilterKey = keyof PostsFilterState;

export function useFilters() {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();

  const filters = getFiltersFromParams(searchParams);

  const setFilter = useCallback(
    <K extends FilterKey>(key: K, value: PostsFilterState[K]) => {
      const params = new URLSearchParams(searchParams.toString());
      const isPageFilter = key === 'page';

      if (
        value === '' ||
        value === null ||
        value === undefined ||
        value === DEFAULT_FILTERS[key]
      ) {
        params.delete(key);
      } else {
        params.set(key, String(value));
      }

      // Reset page when changing non-page filters
      if (!isPageFilter) {
        params.delete('page');
      }

      const qs = params.toString();
      router.push(qs ? `${pathname}?${qs}` : pathname);
    },
    [searchParams, pathname, router]
  );

  const resetFilters = useCallback(() => {
    router.push(pathname);
  }, [pathname, router]);

  return { filters, setFilter, resetFilters };
}
