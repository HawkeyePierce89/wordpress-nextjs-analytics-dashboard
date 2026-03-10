'use client';

import Link from 'next/link';
import type { Post } from '@/types';

type PostMetadataCardProps = {
  post: Post;
};

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

export function PostMetadataCard({ post }: PostMetadataCardProps) {
  const firstCategory = post.categories[0];

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-sm font-semibold text-gray-200">Metadata</h2>
        <Link
          href={`/posts/${post.id}/edit`}
          className="text-xs text-blue-400 hover:text-blue-300 transition-colors"
        >
          Edit
        </Link>
      </div>
      <dl className="space-y-3">
        <div className="flex items-start justify-between">
          <dt className="text-xs text-gray-400">Author</dt>
          <dd className="text-xs text-gray-200 text-right">{post.author.name}</dd>
        </div>
        <div className="flex items-start justify-between">
          <dt className="text-xs text-gray-400">Category</dt>
          <dd className="text-xs text-right">
            {firstCategory ? (
              <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-900/50 text-blue-400">
                {firstCategory.name}
              </span>
            ) : (
              <span className="text-gray-500">—</span>
            )}
          </dd>
        </div>
        <div className="flex items-start justify-between">
          <dt className="text-xs text-gray-400">Published</dt>
          <dd className="text-xs text-gray-200">{formatDate(post.publishedAt)}</dd>
        </div>
        <div className="flex items-start justify-between">
          <dt className="text-xs text-gray-400">Modified</dt>
          <dd className="text-xs text-gray-200">{formatDate(post.updatedAt)}</dd>
        </div>
        <div className="flex items-start justify-between">
          <dt className="text-xs text-gray-400">Reading Time</dt>
          <dd className="text-xs text-gray-200">{post.readingTimeMinutes} min</dd>
        </div>
        <div className="flex items-start justify-between">
          <dt className="text-xs text-gray-400">Featured</dt>
          <dd className="text-xs text-gray-200">{post.isFeatured ? 'Yes' : 'No'}</dd>
        </div>
      </dl>
    </div>
  );
}
