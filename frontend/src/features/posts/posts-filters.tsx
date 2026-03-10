'use client';

import { useState, useEffect } from 'react';
import type { PostsFilterState, Author, Category } from '@/types';

type PostsFiltersProps = {
  filters: PostsFilterState;
  setFilter: <K extends keyof PostsFilterState>(key: K, value: PostsFilterState[K]) => void;
  resetFilters: () => void;
  authors: Author[];
  categories: Category[];
};

export function PostsFilters({
  filters,
  setFilter,
  resetFilters,
  authors,
  categories,
}: PostsFiltersProps) {
  const [searchValue, setSearchValue] = useState(filters.search);

  // Sync local state if external filters reset
  useEffect(() => {
    setSearchValue(filters.search);
  }, [filters.search]);

  function handleSearchChange(e: React.ChangeEvent<HTMLInputElement>) {
    const val = e.target.value;
    setSearchValue(val);
    setFilter('search', val);
  }

  const inputClass =
    'bg-gray-800 border border-gray-700 text-gray-100 text-sm rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500';

  return (
    <div className="flex flex-wrap items-center gap-3">
      <input
        type="text"
        placeholder="Search posts..."
        value={searchValue}
        onChange={handleSearchChange}
        className={`${inputClass} w-52`}
      />

      <select
        value={filters.status}
        onChange={(e) =>
          setFilter('status', e.target.value as PostsFilterState['status'])
        }
        className={inputClass}
      >
        <option value="">All Statuses</option>
        <option value="draft">Draft</option>
        <option value="published">Published</option>
        <option value="scheduled">Scheduled</option>
      </select>

      <select
        value={filters.categoryId}
        onChange={(e) =>
          setFilter(
            'categoryId',
            e.target.value ? Number(e.target.value) : ''
          )
        }
        className={inputClass}
      >
        <option value="">All Categories</option>
        {categories.map((c) => (
          <option key={c.id} value={c.id}>
            {c.name}
          </option>
        ))}
      </select>

      <select
        value={filters.authorId}
        onChange={(e) =>
          setFilter('authorId', e.target.value ? Number(e.target.value) : '')
        }
        className={inputClass}
      >
        <option value="">All Authors</option>
        {authors.map((a) => (
          <option key={a.id} value={a.id}>
            {a.name}
          </option>
        ))}
      </select>

      <button
        onClick={resetFilters}
        className="text-sm text-blue-400 hover:text-blue-300 transition-colors"
      >
        Reset
      </button>
    </div>
  );
}
